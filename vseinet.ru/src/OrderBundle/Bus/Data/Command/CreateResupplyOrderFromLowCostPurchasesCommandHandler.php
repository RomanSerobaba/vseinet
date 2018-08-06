<?php 

namespace OrderBundle\Bus\Data\Command;

use AccountingBundle\Entity\OurSeller;
use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\OrderTypeCode;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateResupplyOrderFromLowCostPurchasesCommandHandler extends MessageHandler
{
    public function handle(CreateResupplyOrderFromLowCostPurchasesCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /**
         * @var OurSeller $ourSeller
         */
        $ourSeller = $em->getRepository(OurSeller::class)->findOneBy(['isDefault' => true,]);

        if (!$ourSeller) {
            throw new NotFoundHttpException('Продавец по умолчанию не найден');
        }

        if ($command->typeCode !== OrderTypeCode::RESUPPLY) {
            throw new BadRequestHttpException('Не верный тип кода');
        }

        $em->getConnection()->beginTransaction();
        try
        {
            $sql = '
                INSERT INTO "order" ( created_at, created_by, manager_id, our_seller_counteragent_id, geo_point_id, type_code, geo_city_id, registered_at, registered_by ) 
                SELECT
                    now( ),
                    :user_id::INTEGER,
                    :user_id::INTEGER,
                    :our_counteragent_id::INTEGER,
                    :destination_point_id::INTEGER,
                    :code,
                    :geo_city_id::INTEGER,
                    now(), 
                    :user_id::INTEGER
                RETURNING id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('our_counteragent_id', $ourSeller->getCounteragentId());
            $statement->bindValue('destination_point_id', $this->getParameter('default.point.id'));
            $statement->bindValue('code', $command->typeCode);
            $statement->bindValue('geo_city_id', $this->getParameter('default.city.id'));
            $statement->execute();

            $orderId = $statement->fetchColumn();

            $this->get('uuid.manager')->saveId($command->uuid, $orderId);

            if (empty($orderId)) {
                throw new NotFoundHttpException('Указанного счета поставщика не существует или он уже закрыт для редактирования');
            }

            $values = [];
            foreach ($command->items as $item) {
                $values[] = sprintf('(%u::INTEGER, %u::INTEGER, %u::INTEGER)', $item['baseProductId'], $item['quantity'], $orderId);
            }

            $sql = '
                WITH DATA ( base_product_id, quantity, order_id ) AS 
                ( VALUES '.implode(',', $values).') 
                INSERT INTO "order_item" ( order_id, base_product_id, quantity, created_at, created_by ) 
                SELECT
                    order_id,
                    base_product_id,
                    quantity,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    DATA 
                RETURNING id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->execute();

            $orderItemIds = $statement->fetchColumn();

            if (empty($orderItemIds)) {
                throw new NotFoundHttpException('Позиции заказа не созданы');
            }

            $sql = '
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    oi.base_product_id,
                    oi.quantity,
                    oi.id,
                    oi.order_id,
                    :order,
                    o.registered_at,
                    :order_creation,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    order_item AS oi
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    oi.id IN (:order_item_ids)
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('order', DocumentTypeCode::ORDER);
            $statement->bindValue('order_creation', OperationTypeCode::ORDER_CREATION);
            $statement->bindValue('order_item_ids', $orderItemIds);
            $statement->execute();

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}
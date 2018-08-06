<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use OrgBundle\Entity\Representative;
use OrderBundle\Entity\OrderDoc;
use GeoBundle\Entity\GeoPoint;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\DocumentTypeCode;
use Doctrine\ORM\Id\SequenceGenerator;
use DateTime;

class CreateResupplyOrderCommandHandler extends MessageHandler
{
    public function handle(CreateResupplyOrderCommand $command)
    {
        /** @var  \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $representative = $em->getRepository(Representative::class)->find($command->id);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->id));
        }

        $geoPoint = $em->getRepository(GeoPoint::class)->find($representative->getGeoPointId());

        foreach ($command->products as $product) {
            $baseProduct = $em->getRepository(BaseProduct::class)->find($product['id']);
            if (!$baseProduct instanceof BaseProduct) {
                throw new NotFoundHttpException(sprintf('Товар %d не найден', $product['id']));
            }

            if (!$product['quantity'] or !is_integer($product['quantity'])) {
                throw new BadRequestHttpException(sprintf('У товара %d не определено количество', $product['id']));
            }
        }

        /** @var User $user */
        $user = $this->get('user.identity')->getUser();

        $order = new OrderDoc();
        $order->setCreatedAt(new DateTime());
        $order->setCreatedBy($user->getId());
        $order->setRegisteredAt(new DateTime());
        $order->setRegisteredBy($user->getId());
        $order->setGeoCityId($geoPoint->getGeoCityId());
        $order->setGeoPointId($representative->getGeoPointId());
        $order->setOrderTypeCode(OrderTypeCode::RESUPPLY);
        
        $sequence = 'order_id_seq';
        $sequenceGenerator = new SequenceGenerator($sequence, 1);
        $number = $sequenceGenerator->generate($em, $order);
        $order->setNumber($number);
        $order->setTitle("Заказ №" . $number);
        $em->persist($order);

        $this->get('uuid.manager')->saveId($command->uuid, $order->getDid());

        $parameters = $placeholders = [];
        foreach ($command->products as $key => $product) {
            $parameters['baseProductId' . $key] = $product['id'];
            $parameters['quantity' . $key] = $product['quantity'];
            $placeholders[] = "(:baseProductId{$key}::integer, :quantity{$key}::integer)";
        }
        $parameters = array_merge($parameters, [
            'orderId' => $number, 
            'createdAt' => date('Y-m-d H:i:s'), 
            'createdBy' => $user->getId(),
        ]);
        $placeholders = implode(',', $placeholders);

        $q = $this->getDoctrine()->getManager()->getConnection()->prepare("
            WITH 
                data (base_product_id, quantity) AS (
                    VALUES {$placeholders}
                )
            INSERT INTO order_item (order_id, base_product_id, quantity, created_at, created_by)
            SELECT 
                :orderId::integer,
                data.base_product_id,
                data.quantity,
                :createdAt::timestamp,
                :createdBy::integer
            FROM data
        ");
        $q->execute($parameters);

        $q = $this->getDoctrine()->getManager()->getConnection()->prepare("
            INSERT INTO goods_need_register (base_product_id, delta, order_item_id, created_at, created_by, registrator_id, registrator_type_code, register_operation_type_code, registered_at)
            SELECT 
                oi.base_product_id, 
                oi.quantity, 
                oi.id, 
                :createdAt::timestamp,
                :createdBy::integer,
                :orderId::integer, 
                :registratorTypeCode::document_type_code, 
                'order_creation', 
                o.registered_at
            FROM order_item AS oi
            JOIN \"order\" AS o ON o.id = oi.order_id
            WHERE oi.order_id = :orderId::integer
        ");
        $q->execute([
            'orderId' => $number, 
            'createdAt' => date('Y-m-d H:i:s'), 
            'createdBy' => $user->getId(),
            'registratorTypeCode' => DocumentTypeCode::ORDER,
        ]);

    }
}
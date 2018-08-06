<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CloseShippingCommandHandler extends MessageHandler
{
    public function handle(CloseShippingCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $sql = '
            UPDATE supplier_reserve 
            SET closed_at = now(),
            closed_by = :user_id::INTEGER 
            WHERE
                closed_at IS NULL 
                AND supplier_id = :supplier_id 
                AND is_shipping = TRUE 
            RETURNING id
        ';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('supplier_id', $command->id);
        $statement->execute();

        $reserveId = $statement->fetchColumn();

        if ($reserveId) {
            // Подчищаем регистры
            $sql = '
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    base_product_id,
                    SUM ( srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    :supplier_reserve,
                    sr.closed_at,
                    :supplier_reserve_closing,
                    now(), 
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supplier_reserve AS sr ON sr.supplier_id = srr.supplier_reserve_id 
                WHERE
                    sr.id = :reserve_id 
                GROUP BY
                    srr.order_item_id,
                    srr.base_product_id,
                    sr.id
            ';

            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('reserve_id', $reserveId);
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supplier_reserve_closing', OperationTypeCode::SUPPLIER_RESERVE_CLOSING);
            $statement->execute();

            $sql = '
                INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_id, supplier_reserve_id, supply_id, purchase_price, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    base_product_id,
                    SUM ( - srr.delta ),
                    srr.order_item_id,
                    srr.supplier_id,
                    sr.id,
                    srr.supply_id,
                    srr.purchase_price,
                    sr.id,
                    :supplier_reserve,
                    sr.closed_at,
                    :supplier_reserve_closing,
                    now(), 
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supplier_reserve AS sr ON sr.supplier_id = srr.supplier_reserve_id 
                WHERE
                    sr.id = :reserve_id 
                GROUP BY
                    srr.order_item_id,
                    srr.supplier_id,
                    srr.purchase_price,
                    srr.base_product_id,
                    sr.id,
                    srr.supply_id
            ';

            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('reserve_id', $reserveId);
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supplier_reserve_closing', OperationTypeCode::SUPPLIER_RESERVE_CLOSING);
            $statement->execute();
        }
    }
}
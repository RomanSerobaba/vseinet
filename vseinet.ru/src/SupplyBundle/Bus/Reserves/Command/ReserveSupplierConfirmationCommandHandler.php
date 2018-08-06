<?php 

namespace SupplyBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \ServiceBundle\Components\Number;

class ReserveSupplierConfirmationCommandHandler extends MessageHandler
{
    public function handle(ReserveSupplierConfirmationCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        if ($command->newPurchasePrice <= 0 || $command->oldPurchasePrice <= 0) {
            throw new BadRequestHttpException('Недопустимое значение цены');
        }

        $em->getConnection()->beginTransaction();
        try {

            if (empty($command->newQuantity) && empty($command->oldQuantity)) {
                $statement = $em->getConnection()->prepare('
                    UPDATE supplier_product 
                    SET price = :newPurchasePrice 
                    WHERE
                        base_product_id = ( SELECT base_product_id FROM order_item WHERE id = :orderItemId ) 
                        AND supplier_id = ( SELECT supplier_id FROM supplier_reserve WHERE id = :supplierReserveId )
                ');
                $statement->bindValue('supplierReserveId', $command->id);
                $statement->bindValue('orderItemId', $command->orderItemId);
                $statement->bindValue('newPurchasePrice', $command->newPurchasePrice);
                $statement->execute();

                $statement = $em->getConnection()->prepare('
                    UPDATE base_product 
                    SET supplier_price = :newPurchasePrice 
                    WHERE
                        id = ( SELECT base_product_id FROM order_item WHERE id = :orderItemId ) 
                        AND supplier_id = ( SELECT supplier_id FROM supplier_reserve WHERE id = :supplierReserveId )
                ');
                $statement->bindValue('supplierReserveId', $command->id);
                $statement->bindValue('orderItemId', $command->orderItemId);
                $statement->bindValue('newPurchasePrice', $command->newPurchasePrice);
                $statement->execute();
            } elseif ($command->newQuantity == $command->oldQuantity) {
                $sql = '
                    SELECT
                        SUM( delta )::INTEGER AS quantity 
                    FROM
                        supplier_reserve_register 
                    WHERE
                        order_item_id = :orderItemId 
                        AND supplier_reserve_id = :supplierReserveId 
                        AND purchase_price = :oldPurchasePrice
                ';

                $q = $em->createNativeQuery($sql, new ResultSetMapping());
                $q->setParameter('orderItemId', $command->orderItemId);
                $q->setParameter('supplierReserveId', $command->id);
                $q->setParameter('oldPurchasePrice', $command->oldPurchasePrice);

                $rows = $q->getResult('ListAssocHydrator');
                $row = array_shift($rows);
                $reservedQuantity = (int) $row['quantity'];

                if ($reservedQuantity < $command->oldQuantity) {
                    throw new BadRequestHttpException('Вы пытаетесь изменить цену на количество товара, превыщающее зарезервированное');
                } else {
                    $statement = $em->getConnection()->prepare('
                        INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, purchase_price, supply_id, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                        (
                        SELECT
                            oi.base_product_id,
                            - SUM( srr.delta ),
                            oi.id,
                            sr.id,
                            srr.purchase_price,
                            srr.supply_id,
                            sr.supplier_id,
                            sr.id,
                            :supplier_reserve :: document_type_code,
                            sr.created_at,
                            :supplier_reserve_change :: operation_type_code,
                            now( ),
                            :user_id::INTEGER 
                        FROM
                            supplier_reserve_register AS srr
                            JOIN order_item AS oi ON oi.id = srr.order_item_id
                            JOIN supplier_reserve AS sr ON sr.id = :supplierReserveId 
                            AND sr.is_shipping = FALSE 
                            AND sr.closed_at IS NULL 
                        WHERE
                            oi.id = :orderItemId 
                            AND srr.purchase_price = :oldPurchasePrice 
                        GROUP BY
                            oi.id,
                            sr.id,
                            srr.supply_id,
                            srr.purchase_price 
                        HAVING
                            SUM( srr.delta ) > 0 
                        
                        UNION ALL
                        
                        SELECT
                            oi.base_product_id,
                            :newQuantity,
                            oi.id,
                            sr.id,
                            :newPurchasePrice,
                            NULL,
                            sr.supplier_id,
                            sr.id,
                            :supplier_reserve :: document_type_code,
                            sr.created_at,
                            :supplier_reserve_change :: operation_type_code,
                            now( ),
                            :user_id::INTEGER 
                        FROM
                            order_item AS oi
                            JOIN supplier_reserve AS sr ON sr.id = :supplierReserveId 
                            AND sr.is_shipping = FALSE 
                            AND sr.closed_at IS NULL 
                        WHERE
                            oi.id = :orderItemId 
                        )
                    ');
                    $statement->bindValue('supplierReserveId', $command->id, Type::INTEGER);
                    $statement->bindValue('orderItemId', $command->orderItemId, Type::INTEGER);
                    $statement->bindValue('newQuantity', $command->newQuantity, Type::INTEGER);
                    $statement->bindValue('oldQuantity', $command->oldQuantity, Type::INTEGER);
                    $statement->bindValue('newPurchasePrice', $command->newPurchasePrice, Type::INTEGER);
                    $statement->bindValue('oldPurchasePrice', $command->oldPurchasePrice, Type::INTEGER);
                    $statement->bindValue('user_id', $currentUser->getId(), Type::INTEGER);
                    $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                    $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
                    $statement->execute();
                }
            } else {
                if ($command->oldQuantity > $command->newQuantity) {
                    $sql = '
                        SELECT
                            SUM( delta )::INTEGER AS quantity
                        FROM
                            supplier_reserve_register 
                        WHERE
                            order_item_id = :orderItemId 
                            AND supplier_reserve_id = :supplierReserveId 
                            AND purchase_price = :oldPurchasePrice
                    ';

                    $q = $em->createNativeQuery($sql, new ResultSetMapping());
                    $q->setParameter('orderItemId', $command->orderItemId);
                    $q->setParameter('supplierReserveId', $command->id);
                    $q->setParameter('oldPurchasePrice', $command->oldPurchasePrice);

                    $rows = $q->getResult('ListAssocHydrator');
                    $row = array_shift($rows);
                    $reservedQuantity = (int) $row['quantity'];

                    if ($reservedQuantity < $command->oldQuantity - $command->newQuantity) {
                        throw new BadRequestHttpException('Вы пытаетесь сбросить большее количество резерва, чем есть по факту');
                    }
                } elseif ($command->oldQuantity < $command->newQuantity) {
                    $sql = '
                        SELECT 
                            SUM( delta )::INTEGER AS quantity
                        FROM
                            goods_need_register 
                        WHERE
                            order_item_id = :order_item_id
                    ';

                    $q = $em->createNativeQuery($sql, new ResultSetMapping());
                    $q->setParameter('order_item_id', $command->orderItemId);

                    $rows = $q->getResult('ListAssocHydrator');
                    $row = array_shift($rows);
                    $needQuantity = (int) $row['quantity'];

                    if ($needQuantity < $command->newQuantity) {
                        throw new BadRequestHttpException('Вы пытаетесь зарезервировать количество больше необходимого');
                    }
                }
                
                $statement = $em->getConnection()->prepare('
                    INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        oi.base_product_id,
                        -:newQuantity :: integer,
                        oi.id,
                        sr.id,
                        :supplier_reserve :: document_type_code,
                        sr.created_at,
                        :supplier_reserve_change :: operation_type_code,
                        now( ),
                        :user_id::INTEGER 
                    FROM
                        order_item AS oi
                        JOIN supplier_reserve AS sr ON sr.id = :supplierReserveId 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    WHERE
                        oi.id = :orderItemId
                ');
                $statement->bindValue('newQuantity', $command->newQuantity, Type::INTEGER);
                $statement->bindValue('supplierReserveId', $command->id, Type::INTEGER);
                $statement->bindValue('orderItemId', $command->orderItemId, Type::INTEGER);
                $statement->bindValue('user_id', $currentUser->getId(), Type::INTEGER);
                $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
                $statement->execute();

                $statement = $em->getConnection()->prepare('
                    INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, purchase_price, supply_id, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        oi.base_product_id,
                        :newQuantity :: integer,
                        oi.id,
                        sr.id,
                        :newPurchasePrice :: integer,
                        NULL,
                        sr.supplier_id,
                        sr.id,
                        :supplier_reserve :: document_type_code,
                        sr.created_at,
                        :supplier_reserve_change :: operation_type_code,
                        now( ),
                        :user_id::INTEGER 
                    FROM
                        order_item AS oi
                        JOIN supplier_reserve AS sr ON sr.id = :supplierReserveId 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    WHERE
                        oi.id = :orderItemId
                ');
                $statement->bindValue('newPurchasePrice', $command->newPurchasePrice, Type::INTEGER);
                $statement->bindValue('newQuantity', $command->newQuantity, Type::INTEGER);
                $statement->bindValue('supplierReserveId', $command->id, Type::INTEGER);
                $statement->bindValue('orderItemId', $command->orderItemId, Type::INTEGER);
                $statement->bindValue('user_id', $currentUser->getId(), Type::INTEGER);
                $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
                $statement->execute();
            }

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}
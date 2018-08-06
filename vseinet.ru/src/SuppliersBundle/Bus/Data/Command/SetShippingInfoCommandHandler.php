<?php 

namespace SuppliersBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\DBAL\Types\Type;

class SetShippingInfoCommandHandler extends MessageHandler
{
    public function handle(SetShippingInfoCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $em->getConnection()->beginTransaction();
        try {
            $sql = '
                UPDATE supplier 
                SET order_threshold_time = :orderThresholdTime 
                WHERE
                    id = :supplierId
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supplierId', $command->id);
            $statement->bindValue('orderThresholdTime', $command->orderThresholdTime, Type::DATETIME);
            $statement->execute();

            $sql = '
                UPDATE supplier_reserve 
                    SET order_delivery_time = :orderDeliveryTime 
                WHERE
                    supplier_id = :supplierId 
                    AND closed_at IS NULL 
                    AND is_shipping = ( SELECT is_shipping FROM supplier_reserve WHERE supplier_id = :supplierId AND closed_at IS NULL AND is_shipping = TRUE )
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supplierId', $command->id);
            $statement->bindValue('orderDeliveryTime', $command->orderDeliveryTime, Type::DATETIME);
            $statement->execute();

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}
<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StartShippingCommandHandler extends MessageHandler
{
    public function handle(StartShippingCommand $command)
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
            UPDATE supplier_reserve AS sr 
            SET is_shipping = TRUE 
            WHERE
                sr.supplier_id = :supplier_id 
                AND sr.is_shipping = FALSE 
                AND sr.closed_at IS NULL 
                AND NOT EXISTS ( SELECT ID FROM supplier_reserve WHERE supplier_id = sr.supplier_id AND sr.is_shipping = TRUE AND sr.closed_at IS NULL ) 
            RETURNING sr.order_delivery_time        
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('supplier_id', $command->id);
        $statement->execute();

        $orderDeliveryTime = $statement->fetchColumn();

        if (!empty($orderDeliveryTime)) {
            $q = $em->createNativeQuery("
                SELECT
                    order_delivery_schedule,
                    order_threshold_schedule,
                    order_threshold_time 
                FROM
                    supplier 
                WHERE
                    id = :supplier_id
            ", new ResultSetMapping());

            $q->setParameter('supplier_id', $command->id);

            $rows = $q->getResult('ListAssocHydrator');
            $supplier = array_shift($rows);

            if (!empty($supplier['order_delivery_schedule'])) {
                $cron = \Cron\CronExpression::factory($supplier['order_delivery_schedule']);

                $nextOrderDeliveryTime = new \DateTime($cron->getNextRunDate($orderDeliveryTime, 0)->format('Y-m-d H:i:s'));

                $sql = '
                    INSERT INTO supplier_reserve ( supplier_id, created_at, created_by, is_shipping, order_delivery_time )
                    VALUES (:supplier_id::INTEGER, NOW(), :user_id::INTEGER, FALSE, :next_order_delivery_time)
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supplier_id', $command->id);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('next_order_delivery_time', $nextOrderDeliveryTime, Type::DATETIME);
                $statement->execute();
            }

            if (!empty($supplier['order_threshold_schedule']) && !empty($supplier['order_threshold_time'])) {
                $cron = \Cron\CronExpression::factory($supplier['order_threshold_schedule']);

                $nextOrderThresholdTime = new \DateTime($cron->getNextRunDate($supplier['order_threshold_time'], 0)->format('Y-m-d H:i:s'));

                $sql = '
                    UPDATE supplier 
                    SET order_threshold_time = :next_order_threshold_time 
                    WHERE
                        id = :supplier_id
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supplier_id', $command->id);
                $statement->bindValue('next_order_threshold_time', $nextOrderThresholdTime, Type::DATETIME);
                $statement->execute();
            }
        } else {
            throw new BadRequestHttpException('Отгрузка уже начата');
        }
    }
}
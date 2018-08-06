<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class MarkReachedCommandHandler extends MessageHandler
{
    public function handle(MarkReachedCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $statement = $em->getConnection()->prepare('
            UPDATE "client_order" SET is_not_reached = :is_checked::boolean 
            WHERE order_id = :order_id AND is_not_reached != :is_checked::boolean 
            RETURNING order_id
        ');
        $statement->bindValue('is_checked', $command->value, "boolean");
        $statement->bindValue('order_id', $command->id);
        $statement->execute();

        $id = $statement->fetchColumn();

        if ($id > 0) {
            $q = $em->createNativeQuery('
                SELECT
                    vup.mobile 
                FROM
                    "order" o
                    INNER JOIN func_view_user_person(o.created_by) vup ON vup.user_id = o.created_by
                WHERE
                    o.id = :id 
            ', new ResultSetMapping());

            $q->setParameter('id', $command->id);

            $rows = $q->getResult('ListAssocHydrator');
            $row = array_shift($rows);
            $mobile = isset($row['mobile']) ? $row['mobile'] : '';

            if ($mobile) {
                $mobile = str_replace(['{', '}',], '', $mobile);

                if ($mobile != 'NULL') {
                    $mobile = explode(',', $mobile);
                    $phone = array_shift($mobile);

                    $data = [
                        'phone' => $phone,
                        'type' => 7,
                        'order_id' => $command->id,
                        'text' => 'Менеджер не дозвонился по заказу '.$command->id.'.Позвоните по т.290708',
                    ];

                    // $sender = $this->get('service.sender');
                    // $sender->send('not_reached', $data, true);
                }
            }
        }
    }
}
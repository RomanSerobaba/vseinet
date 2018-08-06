<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\NotificationLogStatus;
use AppBundle\Enum\RepresentativeTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;

class GetSmsLogsQueryHandler extends MessageHandler
{
    public function handle(GetSmsLogsQuery $query) : array
    {
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                nl.created_at AS date /*дата отправки*/,
                nl.channel /*канал отправки*/,
                nl.addressee /*адрес отправки*/,
                nl.text /*текст сообщения*/,
                COALESCE ( cnl.service_name, nl.service_name ) AS service_name /*название сервиса отправки*/,
                COALESCE ( cnl.status, nl.status ) AS status /*текущий статус сообщения*/,
                nl.amount AS message_amount /*количество сообщений*/
            FROM
                notification_log AS nl
                LEFT JOIN notification_log AS cnl ON cnl.pid = nl.id 
                    AND cnl.status != :failed 
            WHERE
                nl.order_id = :order_id 
                AND nl.pid IS NULL 
            ORDER BY
                nl.created_at
        ', new ResultSetMapping());

        $q->setParameter('failed', NotificationLogStatus::FAILED);
        $q->setParameter('order_id', $query->id);

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}
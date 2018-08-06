<?php 

namespace OrderBundle\Bus\Item\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetStatusesLogsQueryHandler extends MessageHandler
{
    /**
     * @param GetStatusesQuery $query
     *
     * @return array
     */
    public function handle(GetStatusesLogsQuery $query) : array
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW OrderBundle\Bus\Item\Query\DTO\Status(
                    ois.name,
                    oisl.updatedAt,
                    oisl.updatedBy
                ) 
            FROM
                OrderBundle:OrderItemStatusLog AS oisl
                JOIN OrderBundle:OrderItemStatus AS ois WITH ois.code = oisl.orderItemStatusCode
            ORDER BY
                oisl.updatedAt
        ');

        return $q->getArrayResult();
    }
}
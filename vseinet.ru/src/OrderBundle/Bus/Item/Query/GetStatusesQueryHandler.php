<?php 

namespace OrderBundle\Bus\Item\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetStatusesQueryHandler extends MessageHandler
{
    /**
     * @param GetStatusesQuery $query
     *
     * @return array
     */
    public function handle(GetStatusesQuery $query) : array
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW OrderBundle\Bus\Item\Query\DTO\Status(
                    ois.code,
                    ois.name
                ) 
            FROM
                OrderBundle:OrderItemStatus AS ois
            ORDER BY
                ois.code
        ');

        return $q->getArrayResult();
    }
}
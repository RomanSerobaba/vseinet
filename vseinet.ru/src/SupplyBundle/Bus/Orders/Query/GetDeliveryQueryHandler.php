<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetDeliveryQueryHandler extends MessageHandler
{
    /**
     * @param GetDeliveryQuery $query
     *
     * @return array
     */
    public function handle(GetDeliveryQuery $query) : array
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                dt.code, 
                dt.name
            FROM
                OrderBundle:DeliveryType AS dt
            WHERE
                dt.isActive = TRUE
        ');

        return $q->getArrayResult();
    }
}
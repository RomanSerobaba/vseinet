<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetPaymentsQueryHandler extends MessageHandler
{
    /**
     * @param GetPaymentsQuery $query
     *
     * @return array
     */
    public function handle(GetPaymentsQuery $query) : array
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                pt.code, 
                pt.name  
            FROM
                OrderBundle:PaymentType AS pt
            WHERE
                pt.isActive = TRUE
        ');

        return $q->getArrayResult();
    }
}
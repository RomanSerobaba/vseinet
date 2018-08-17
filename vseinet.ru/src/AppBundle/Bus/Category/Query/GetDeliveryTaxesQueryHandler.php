<?php 
namespace AppBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetDeliveryTaxesQueryHandler extends MessageHandler
{
    public function handle(GetDeliveryTaxesQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Category\Query\DTO\DeliveryTax (
                    c.name,
                    c.deliveryTax
                )
            FROM AppBundle:Category AS c 
            WHERE c.deliveryTax != 0
            ORDER BY c.deliveryTax, c.name 
        ");

        return $q->getResult();
    }
}

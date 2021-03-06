<?php 

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetDeliveryTaxesQueryHandler extends MessageHandler
{
    public function handle(GetDeliveryTaxesQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Geo\Query\DTO\DeliveryTax (
                    gp.id,
                    r.type,
                    gr.id,
                    gr.name,
                    gc.id,
                    gc.name,
                    ga.address,
                    r.hasRetail,
                    r.deliveryTax
                ),
                CASE WHEN gc.isCentral = true THEN 1 ELSE 2 END AS HIDDEN ORD
            FROM AppBundle:Representative AS r 
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
            INNER JOIN AppBundle:GeoCity AS gc WITH gc.id = gp.geoCityId 
            INNER JOIN AppBundle:GeoRegion AS gr WITH gr.id = gc.geoRegionId
            LEFT OUTER JOIN AppBundle:GeoAddress AS ga WITH ga.id = gp.geoAddressId 
            WHERE r.isActive = true AND r.hasDelivery = true
            ORDER BY ORD, gc.name  
        ");

        $representatives = $q->getResult();

        $grouped = [];
        foreach ($representatives as $representative) {
            $grouped[$representative->geoRegionId][$representative->type][] = $representative;
        }

        return $grouped;
    }
}

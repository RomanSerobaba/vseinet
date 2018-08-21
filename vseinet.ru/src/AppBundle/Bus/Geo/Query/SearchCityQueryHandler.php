<?php 

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchCityQueryHandler extends MessageHandler
{
    public function handle(SearchCityQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Geo\Query\DTO\CityFound (
                    gc.id, 
                    CONCAT(gc.name, ' ', gc.unit),
                    CONCAT(gr.name, ' ', gr.unit),
                    CONCAT(ga.name, ' ', ga.unit)
                ),
                CASE WHEN gc.isListed = true THEN 1 ELSE 2 AS HIDDEN ORD
            FROM AppBundle:GeoCity AS gc
            INNER JOIN AppBundle:GeoRegion AS gr WITH gr.id = gc.geoRegionId 
            LEFT OUTER JOIN AppBundle:GeoArea AS ga WITH ga.id = gc.geoAreaId 
            WHERE LOWER(gc.name) LIKE LOWER(:name) AND gc.id > 0
            ORDER BY ORD, gc.AOLEVEL, gc.name 
        ");
        $q->setParameter('name', $query->q.'%');
        $q->setMaxResults($query->limit);
        $geoCities = $q->getResult();

        return $geoCities; 
    }
}
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
                    c.id, 
                    c.name 
                )
            FROM AppBundle:GeoCity as c 
            WHERE LOWER(c.name) LIKE LOWER(:name)
            ORDER BY c.\"AOLEVEL\", c.name 
        ");
        $q->setParameter('name', $query->q);
        $q->setMaxResults($query->limit);
        $cities = $q->getArrayResult();

        return $cities; 
    }
}
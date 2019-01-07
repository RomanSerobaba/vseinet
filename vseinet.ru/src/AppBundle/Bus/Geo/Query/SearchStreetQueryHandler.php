<?php

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchStreetQueryHandler extends MessageHandler
{
    public function handle(SearchStreetQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Geo\Query\DTO\StreetFound (
                    gs.id,
                    gs.name,
                    gs.unit
                )
            FROM AppBundle:GeoStreet AS gs
            WHERE gs.geoCityId = :geoCityId AND LOWER(gs.name) LIKE LOWER(:name) AND gs.id > 0
            ORDER BY gs.name
        ");
        $q->setParameter('geoCityId', $query->geoCityId);
        $q->setParameter('name', $query->q.'%');
        $q->setMaxResults($query->limit);
        $geoStreets = $q->getResult();

        return $geoStreets;
    }
}

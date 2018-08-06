<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAddressQueryHandler extends MessageHandler
{
    public function handle(GetAddressQuery $query)
    {
        $q = $this->getDoctrine()->getManager(->createQuery("
            SELECT
                NEW AppBundle\Bus\User\Query\DTO\Address (
                    ga.id,
                    gr.id,
                    gr.name,
                    gr.unit,
                    gc.id,
                    gc.name,
                    gc.unit,
                    gs.id,
                    gs.name,
                    gs.unit,
                    ga.house,
                    ga.building,
                    ga.apartment,
                    ga.office,
                    gss.id,
                    gss.name,
                    ga.floor,
                    ga.hasLift,
                    gss.name,
                    ga.coordinates,
                    ga.comment,
                    u2ga.isDefault
                )
            FROM AppBundle:UserToAddress AS u2ga
            INNER JOIN GeoBundle:GeoAddress AS ga WITH ga.id = u2ga.geoAddressId
            LEFT OUTER JOIN GeoBundle:GeoStreet AS gs WITH gs.id = ga.geoStreetId
            LEFT OUTER JOIN GeoBundle:GeoCity AS gc WITH gc.id = gs.geoCityId
            LEFT OUTER JOIN GeoBundle:GeoRegion gr WITH gr.id = gc.geoRegionId 
            LEFT OUTER JOIN GeoBundle:GeoSubwayStation AS gss WITH gss.id = ga.geoSubwayStationId
            WHERE ga.id = :id
        ");
        $q->setParameter('id', $query->id);
        $address = $q->getSingleResult();

        return $address;
    }
}

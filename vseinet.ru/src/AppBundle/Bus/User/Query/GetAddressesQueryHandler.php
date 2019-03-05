<?php

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAddressesQueryHandler extends MessageHandler
{
    public function handle(GetAddressesQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\User\Query\DTO\Address (
                    a.id,
                    a.postalCode,
                    gr.name,
                    gr.id,
                    gr.unit,
                    ga.name,
                    ga.id,
                    ga.unit,
                    gc.name,
                    gc.id,
                    gc.unit,
                    gs.name,
                    gs.id,
                    gs.unit,
                    a.house,
                    a.building,
                    a.apartment,
                    a.office,
                    a.floor,
                    a.hasLift,
                    a.coordinates,
                    a.address,
                    a.comment,
                    ga2p.isMain
                )
            FROM AppBundle:GeoAddressToPerson AS ga2p
            INNER JOIN AppBundle:GeoAddress AS a WITH a.id = ga2p.geoAddressId
            LEFT OUTER JOIN AppBundle:GeoRegion gr WITH gr.id = a.geoRegionId
            LEFT OUTER JOIN AppBundle:GeoArea AS ga WITH ga.id = a.geoAreaId
            LEFT OUTER JOIN AppBundle:GeoCity AS gc WITH gc.id = a.geoCityId
            LEFT OUTER JOIN AppBundle:GeoStreet AS gs WITH gs.id = a.geoStreetId
            WHERE ga2p.personId = :personId
        ");
        $q->setParameter('personId', $this->getUser()->getPersonId());
        $addresses = $q->getResult();

        return $addresses;
    }
}

<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetAddressQueryHandler extends MessageHandler
{
    public function handle(GetAddressQuery $query)
    {
         $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\User\Query\DTO\Address (
                    a.id,
                    a.postalCode,
                    gr.name,
                    gr.unit,
                    ga.name,
                    ga.unit,
                    gc.name,
                    gc.unit,
                    gs.name,
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
            FROM GeoBundle:GeoAddressToPerson AS ga2p
            INNER JOIN GeoBundle:GeoAddress AS a WITH a.id = ga2p.geoAddressId
            LEFT OUTER JOIN GeoBundle:GeoRegion gr WITH gr.id = a.geoRegionId 
            LEFT OUTER JOIN GeoBundle:GeoArea AS ga WITH ga.id = a.geoAreaId
            LEFT OUTER JOIN GeoBundle:GeoCity AS gc WITH gc.id = a.geoCityId
            LEFT OUTER JOIN GeoBundle:GeoStreet AS gs WITH gs.id = a.geoStreetId
            WHERE a.id = :id
        ");
        $q->setParameter('id', $query->id);
        $address = $q->getSingleResult();
        if (!$address instanceof DTO\Address) {
            throw new NotFoundHttpException();
        }

        return $address;
    }
}

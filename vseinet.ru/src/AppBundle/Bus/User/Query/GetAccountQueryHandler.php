<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAccountQueryHandler extends MessageHandler
{
    public function handle(GetAccountQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('user.identity')->getUser();

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\User\Query\DTO\UserInfo (
                    u.id,
                    p.lastname,
                    p.firstname,
                    p.secondname,
                    p.gender,
                    p.birthday,
                    gc.id,
                    gc.name,
                    u.isMarketingSubscribed
                )
            FROM AppBundle:User AS u
            INNER JOIN AppBundle:Person AS p WITH u.personId = p.id
            LEFT OUTER JOIN GeoBundle:GeoCity AS gc WITH gc.id = u.cityId
            WHERE u.id = :id
        ");
        $q->setParameter('id', $user->person->getId());
        $info = $q->getSingleResult();

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\User\Query\DTO\Contact (
                    c.id,
                    c.contactTypeCode,
                    c.value,
                    c.comment,
                    c.isMain
                )
            FROM AppBundle:Contact AS c 
            WHERE c.personId = :personId
            ORDER BY c.contactTypeCode ASC, c.isMain DESC
        ");
        $q->setParameter('personId', $user->person->getId());
        $contacts = $q->getResult();

        $q = $em->createQuery("
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
                    a.comment,
                    ga2p.isMain
                )
            FROM GeoBundle:GeoAddressToPerson AS ga2p
            INNER JOIN GeoBundle:GeoAddress AS a WITH a.id = ga2p.geoAddressId
            LEFT OUTER JOIN GeoBundle:GeoRegion gr WITH gr.id = a.geoRegionId 
            LEFT OUTER JOIN GeoBundle:GeoArea AS ga WITH ga.id = a.geoAreaId
            LEFT OUTER JOIN GeoBundle:GeoCity AS gc WITH gc.id = a.geoCityId
            LEFT OUTER JOIN GeoBundle:GeoStreet AS gs WITH gs.id = a.geoStreetId
            WHERE ga2p.personId = :personId
        ");
        $q->setParameter('personId', $user->person->getId());
        $addresses = $q->getResult();

        return new DTO\Account($info, $contacts, $addresses);
    }
}

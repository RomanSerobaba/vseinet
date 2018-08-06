<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAccountQueryHandler extends MessageHandler
{
    public function handle(GetAccountQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

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
        $q->setParameter('id', $this->get('user.identity')->getUser()->getId());
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
        $q->setParameter('personId', $this->get('user.identity')->getUser()->getPersonId());
        $contacts = $q->getResult();

        $q = $em->createQuery("
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
                    ga.floor,
                    ga.hasLift,
                    ga.coordinates,
                    ga.comment,
                    u2ga.isDefault
                )
            FROM AppBundle:UserToAddress AS u2ga
            INNER JOIN GeoBundle:GeoAddress AS ga WITH ga.id = u2ga.geoAddressId
            LEFT OUTER JOIN GeoBundle:GeoStreet AS gs WITH gs.id = ga.geoStreetId
            LEFT OUTER JOIN GeoBundle:GeoCity AS gc WITH gc.id = gs.geoCityId
            LEFT OUTER JOIN GeoBundle:GeoRegion gr WITH gr.id = gc.geoRegionId 
            WHERE u2ga.userId = :userId
        ");
        $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
        $addresses = $q->getResult();

        return new DTO\Account($info, $contacts, $addresses);
    }
}

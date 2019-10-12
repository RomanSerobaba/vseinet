<?php

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetRepresentativesQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Geo\Query\DTO\Representative (
                    gp.id,
                    gr.id,
                    gr.name,
                    gc.id,
                    gc.name,
                    ga.address,
                    r.hasRetail,
                    r.hasDelivery,
                    r.deliveryTax,
                    r.type
                ),
                CASE WHEN r.isCentral = true THEN 1 ELSE 2 END AS HIDDEN ORD1,
                CASE WHEN r.hasDelivery = true THEN 1 ELSE 2 END AS HIDDEN ORD2
            FROM AppBundle:Representative AS r
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
            INNER JOIN AppBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
            INNER JOIN AppBundle:GeoRegion AS gr WITH gr.id = gc.geoRegionId
            LEFT OUTER JOIN AppBundle:GeoAddress AS ga WITH ga.id = gp.geoAddressId
            WHERE r.isActive = true AND (r.hasRetail = true OR r.hasDelivery = true)
            ORDER BY ORD1, ORD2, gc.name
        ");

        $representatives = $q->getResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT
                rp.representativeId,
                c.value,
                CASE WHEN c.isMain = true THEN 1 ELSE 2 END HIDDEN ORD
            FROM AppBundle:RepresentativePhone rp
            INNER JOIN AppBundle:Contact c WITH c.id = rp.contactId
            WHERE rp.representativeId IN (:representativeIds) AND c.typeCode = :phone
            ORDER BY ORD
        ");
        $q->setParameter('phone', ContactTypeCode::PHONE);
        $phones = $q->getArrayResult();
        foreach ($phones as $phone) {
            $representatives[$phone->representativeId]->phones[] = $phone->value;
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Representative\Query\DTO\Schedule (
                    rs.representativeId,
                    rs.s1, rs.t1,
                    rs.s2, rs.t2,
                    rs.s3, rs.t3,
                    rs.s4, rs.t4,
                    rs.s5, rs.t5,
                    rs.s6, rs.t6,
                    rs.s7, rs.t7
                )
            FROM AppBundle:RepresentativeSchedule rs
            WHERE rs.representativeId IN (:representativeIds)
        ");
        $q->setParameter('representativeIds', array_keys($representatives));
        $schedules = $q->getArrayResult();

        foreach ($schedules as $schedule) {
            $representatives[$schedule->representativeId]->schedule = $schedule;
        }

        $grouped = [];
        foreach ($representatives as $representative) {
            $grouped[$representative->geoRegionId][$representative->type][] = $representative;
        }

        return $grouped;
    }
}

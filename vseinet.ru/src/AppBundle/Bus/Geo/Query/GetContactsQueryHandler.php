<?php 

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ContactTypeCode;

class GetContactsQueryHandler extends MessageHandler
{
    public function handle(GetContactsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW (
                    r.geoPointId,
                    gc.name,
                    gr.name
                ),
                CASE WHEN c.isCentral = true THEN 1 ELSE 2 END HIDDEN ORD 
            FROM AppBundle:Representative r 
            INNER JOIN AppBundle:GeoPoint gp WITH gp.id = r.geoPointId 
            INNER JOIN AppBundle:GeoCity gc WITH gc.id = gp.geoCityId 
            INNER JOIN AppBundle:GeoRegion gr WITH gr.id = gc.geoRegionId
            WHERE r.isActive = true AND (r.hasRetail = true OR r.hasDelivery = true)
            ORDER BY ORD, c.name
        ");
        $representatives = $q->getArrayResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\Representative\Query\DTO\Phone (
                    rp.representativeId,
                    c.value 
                ),
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

        return $representatives;
    }
}

<?php

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\RepresentativePhoto;
use AppBundle\Enum\ContactTypeCode;

class GetRepresentativeQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativeQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Geo\Query\DTO\Representative (
                    gp.id,
                    gp.name,
                    CONCAT(gc.name, ' ', gc.unit),
                    ga.address,
                    r.hasRetail
                )
            FROM AppBundle:Representative AS r
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
            INNER JOIN AppBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
            LEFT OUTER JOIN AppBundle:GeoAddress AS ga WITH ga.id = gp.geoAddressId
            WHERE r.geoPointId = :geoPointId AND r.isActive = true
        ");
        $q->setParameter('geoPointId', $query->geoPointId);
        $representative = $q->getOneOrNullResult();
        if (!$representative instanceof DTO\Representative) {
            throw new NotFoundHttpException();
        }

        $representative->photos = $em->getRepository(RepresentativePhoto::class)->findBy([
            'representativeId' => $query->geoPointId,
        ], [
            'sortOrder' => 'ASC',
        ]);

        $q = $em->createQuery("
            SELECT
                c.value,
                CASE WHEN c.isMain = true THEN 1 ELSE 2 END HIDDEN ORD
            FROM AppBundle:RepresentativePhone rp
            INNER JOIN AppBundle:Contact c WITH c.id = rp.contactId
            WHERE rp.representativeId = :representativeId AND c.contactTypeCode = :phone
            ORDER BY ORD
        ");
        $q->setParameter('representativeId', $query->geoPointId);
        $q->setParameter('phone', ContactTypeCode::PHONE);
        $representative->phones = $q->getArrayResult();

        $q = $em->createQuery("
            SELECT
                rs.s1, rs.t1,
                rs.s2, rs.t2,
                rs.s3, rs.t3,
                rs.s4, rs.t4,
                rs.s5, rs.t5,
                rs.s6, rs.t6,
                rs.s7, rs.t7
            FROM AppBundle:RepresentativeSchedule rs
            WHERE rs.representativeId = :representativeId
        ");
        $q->setParameter('representativeId', $query->geoPointId);
        $schedules = $q->getSingleResult();

        $days = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
        $blocks = [];
        $period = 0;
        $current = '';
        foreach ($days as $i => $day) {
            $since = $schedules['s'.($i + 1)];
            $till = $schedules['t'.($i + 1)];
            $time = $till ? 'с '.$since->format("G:i").' до '.$till->format("G:i") : 'выходной';
            if ($time !== $current) {
                $current = $time;
                $period += 1;
            }
            $blocks[$period]['days'][] = $day;
            $blocks[$period]['time'] = $time;
        }
        foreach ($blocks as $block) {
            $representative->schedule[] = new DTO\ScheduleItem(count($block['days']), $block['time']);
        }

        return $representative;
    }
}

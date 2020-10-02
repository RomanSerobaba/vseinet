<?php

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\RepresentativePhoto;
use AppBundle\Enum\ContactTypeCode;

class GetRepresentativeQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativeQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                gp.id as geo_point_id,
                gp.name as geo_point_name,
                CONCAT(gc.unit, \' \', gc.name) as geo_city_name,
                ga.address,
                r.has_retail,
                r.has_delivery,
                ga.coordinates[0] as longitude,
                ga.coordinates[1] as latitude
            FROM representative AS r
            INNER JOIN geo_point AS gp on gp.id = r.geo_point_id
            INNER JOIN geo_city AS gc on gc.id = gp.geo_city_id
            LEFT OUTER JOIN geo_address AS ga on ga.id = gp.geo_address_id
            WHERE r.geo_point_id = :geoPointId AND r.is_active = true AND (r.has_retail = true OR r.has_delivery = true)
        ', new DTORSM(DTO\Representative::class))
            ->setParameter('geoPointId', $query->geoPointId);
        $representative = $q->getResult('DTOHydrator');

        if (empty($representative)) {
            throw new NotFoundHttpException();
        }

        $representative = $representative[0];
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
        $enDays = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
        $blocks = [];
        $fullBlocks = [];
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
            if ($till) {
                $fullBlocks[$since->format("G:i").'-'.$till->format("G:i")][] = $enDays[$i];
            }
        }
        if ($fullBlocks) {
            uasort($fullBlocks, function($a, $b){ count($a) > count($b) ? -1 : 1; });
            $sch = key($fullBlocks);
            $d = reset($fullBlocks);
            $representative->fullSchedule = implode(',',$d) . ' ' . $sch;
        }
        foreach ($blocks as $block) {
            $representative->schedule[] = new DTO\ScheduleItem(count($block['days']), $block['time']);
        }

        return $representative;
    }
}

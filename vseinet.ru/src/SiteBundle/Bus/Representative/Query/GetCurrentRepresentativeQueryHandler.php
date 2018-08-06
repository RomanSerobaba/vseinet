<?php 

namespace SiteBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ContactTypeCode;

class GetCurrentRepresentativeQueryHandler extends MessageHandler
{
    public function handle(GetCurrentRepresentativeQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $geoCityId = $this->get('session')->get('geo_city_id');
        $q = $em->createQuery("
            SELECT
                NEW SiteBundle\Bus\Representative\Query\DTO\Representative (
                    gc.geoRegionId,
                    gp.geoCityId,
                    gc.name,
                    r.geoPointId,
                    gp.name,
                    r.isCentral,
                    gp.geoAddressId
                ),
                CASE WHEN gc.id = :geoCityId THEN 1 ELSE 2 END AS HIDDEN ORD 
            FROM OrgBundle:Representative AS r 
            INNER JOIN GeoBundle:GeoPoint AS gp WITH gp.id = r.geoPointId 
            INNER JOIN GeoBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
            WHERE r.isActive = true AND gc.id IN (:geoCityId, :defaultGeoCityId) 
            ORDER BY ORD 
        ");
        $q->setParameter('geoCityId', $geoCityId ?: $this->getParameter('default.city.id'));
        $q->setParameter('defaultGeoCityId', $this->getParameter('default.city.id'));
        $q->setMaxResults(1);
        $representative = $q->getSingleResult();
        if ($representative->addressId) {
            $representative->address = $this->get('address.formatter')->format($representative->addressId);
        }

        $q = $em->createQuery("
            SELECT
                c.value,
                CASE WHEN c.isMain = true THEN 1 ELSE CASE WHEN c.contactTypeCode = :phoneOrd THEN 2 ELSE 3 END END AS ORD
            FROM OrgBundle:RepresentativePhone AS rp 
            INNER JOIN AppBundle:Contact AS c WITH c.id = rp.contactId
            WHERE rp.representativeId = :representativeId AND c.contactTypeCode IN (:phone, :mobile)
            ORDER BY ORD 
        ");
        $q->setParameter('representativeId', $representative->pointId);
        $q->setParameter('phoneOrd', ContactTypeCode::PHONE);
        $q->setParameter('phone', ContactTypeCode::PHONE);
        $q->setParameter('mobile', ContactTypeCode::MOBILE);
        $contacts = $q->getArrayResult();
        $this->formatContacts($representative, $contacts);

        $dayOfWeek = date('N');
        $q = $em->createQuery("
            SELECT 
                rs.s{$dayOfWeek} AS s,
                rs.t{$dayOfWeek} AS t
            FROM OrgBundle:RepresentativeSchedule rs 
            WHERE rs.representativeId = :representativeId     
        ");
        $q->setParameter('representativeId', $representative->pointId);
        $schedule = $q->getSingleResult();
        $this->formatSchedule($representative, $schedule);

        if ($representative->isCentral) {
            $q = $em->createQuery("
                SELECT 
                    COUNT(r) AS countPoints,
                    SUM(CASE WHEN TIMESTAMPDIFF(MONTH, r.openingDate, CURRENT_TIMESTAMP()) < 2 THEN 1 ELSE 0 END) AS countNewPoints
                FROM OrgBundle:Representative AS r 
                INNER JOIN GeoBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
                INNER JOIN GeoBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
                WHERE gc.geoRegionId = :regionId AND r.isActive = true AND (r.hasRetail = true OR r.hasDelivery = true)
            ");
            $q->setParameter('regionId', $representative->regionId);
            $counters = $q->getSingleResult();
            $representative->countPoints = $counters['countPoints'];
            $representative->countNewPoints = $counters['countNewPoints'];
        }

        return $representative;
    }

    protected function formatContacts($representative, $contacts)
    {
        if (1 === count($contacts)) {
            $representative->phoneTop1 = $contacts[0]['value'];
            $representative->phoneFoolter = $contacts[0]['value'];

            return $representative;
        }

        foreach ($contacts as &$contact) {
            $contact['value'] = $this->get('phone.formater')->format($contact['value']);
        }

        $contacts = array_filter($contacts, function($contact) {
            return $contact['value'];
        });

        $matches = [];
        foreach ($contacts as $index => $contact) {
            $matches[$index] = [];
            if (!preg_match('/\((\d+)\)\s*(.+)/uD', $contact['value'], $matches[$index])) {
                $matches[$index][1] = '';
                $matches[$index][2] = '';    
            }
            $representative->contacts[] = $contact;
        }

        if (2 === count($contacts)) {
            $representative->phoneTop1 = $contacts[0]['value'];
            $representative->phoneTop2 = $contacts[1]['value'];
            $representative->phoneFoolter = $contacts[0]['value'].', ';
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phoneFoolter = $matches[1][2];
            }
            else {
                $representative->phoneFooter = $contacts[1]['value'];
            }

            return $representative;
        }
        
        if ($matches[0][1]) {
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phoneTop1 = '+7 ('.$matches[0][1].') '.$matches[0][2].', '.$matches[1][2];
                $representative->phoneTop2 = $contacts[2]['value'];
                if ($matches[0][1] == $matches[2][1]) {
                    $representative->phoneFooter = $representative->phoneTop1.', '.$matches[2][2];    
                }
            }
            elseif ($matches[0][1] == $matches[2][1]) {
                $representative->phoneTop1 = '+7 ('.$matches[0][1].') '.$matches[0][2].', '.$matches[2][2];
                $representative->phoneTop2 = $contacts[1]['value'];

            }
            else {
                $representative->phoneTop1 = $contacts[0]['value'];
                $representative->phoneTop2 = $contacts[1]['value'];
            }
        }
        elseif ($matches[1][1] && $matches[1][1] == $matches[2][1]) {
            $representative->phoneTop1 = '+7 ('.$matches[1][1].') '.$matches[1][2].', '.$matches[2][2];
            $representative->phoneTop2 = $contacts[0]['value'];
        }
        if (null === $representative->phoneFooter) {
            $representative->phoneFooter = $representative->phoneTop1.', '.$representative->phoneTop2;
        }

        return $representative;
    }

    protected function formatSchedule($representative, $schedule)
    {
        if (!$schedule['s']) {
            $representative->schedule = 'выходной';
        }
        elseif (0 === $schedule['s']->getTimestamp() % 3600 && 0 === $schedule['t']->getTimestamp() % 36000) {
            $representative->schedule = $schedule['s']->format('G').' - '.$schedule['t']->format('G');
        }
        else {
            $representative->schedule = $schedule['s']->format('G:i').' - '.$schedule['t']->format('G:i');
        }

        return $representative;
    }
}
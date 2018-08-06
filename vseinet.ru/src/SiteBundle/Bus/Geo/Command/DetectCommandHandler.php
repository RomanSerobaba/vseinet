<?php 

namespace SiteBundle\Bus\Geo\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ContactTypeCode;

class DetectCommandHandler extends MessageHandler
{
    public function handle(DetectCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $geoCityId = $this->get('session')->get('geo_city_id');
        if (null === $geoCityId) {
            $ip = $this->get('request')->getClientIp();
            $q = $em->createQuery("
                SELECT gi.geoCityId 
                FROM GeoBundle:GeoIp gi 
                WHERE gi.longIp1 <= :ip1 AND gi.longIp2 >= :ip2 
            ");
            $q->setParameter('ip1', ip2long($ip));
            $q->setParameter('ip2', ip2long($ip));
            $q->setMaxResults(1);
            $geoCityId = $q->getOneOrNullResult() ?: 0;
            $this->get('session')->set('geo_city_id', $geoCityId);
        }

        $date = $this->get('session')->get('current_date');
        if (date('Y-m-d') !== $date) {
            $this->get('session')->set('current_date', $date);
            $q = $em->createQuery("
                SELECT
                    NEW SiteBundle\Bus\Geo\Command\DTO\Representative(
                        r.geoPointId,
                        gp.name,
                        r.isCentral,
                        gc.name
                    ),
                    CASE WHEN gc.id = :geoCityId THEN 1 ELSE 2 END HIDDEN ORD 
                FROM OrgBundle:Representative r 
                INNER JOIN GeoBundle:GeoPoint gp WITH gp.id = r.geoPointId 
                INNER JOIN GeoBundle:GeoCity gc WITH gc.id = gp.getCityId
                WHERE r.isActive = true AND r.isCentral = true AND gc.id IN (:geoCityId, :defaultGeoCityId) 
                ORDER BY ORD 
            ");
            $q->setParameter('geoCityId', $geoCityId ?: $this->getParameter('default.city.id'));
            $q->setParameter('defaultGeoCityId', $this->getParameter('default.city.id'));
            $q->setMaxResults(1);
            $representatives = $q->getSingleResult();
            $representative = array_shift($representatives);
            $representative->countPoints = count($representatives);

            $q = $em->createQuery("
                SELECT
                    c.value,
                    CASE WHEN c.isMain THEN 1 ELSE 2 END AS ORD 
                FROM OrgBundle:RepresentativePhone rp 
                INNER JOIN AppBundle:Contact c WITH c.id = rp.contactId
                WHERE rp.representativeId = :representativeId AND c.typeCode = :phone
                ORDER BY ORD 
            ");
            $q->setParameter('representativeId', $representative->getGeoPointId());
            $q->setParameter('phone', ContactTypeCode::PHONE);
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
            $q->setParameter('representativeId', $representative->getGeoPointId);
            $schedule = $q->getSingleResult();
            $this->formatSchedule($representative, $schedule);

            $this->get('session')->set('representative', $representative);
        }
    }

    protected function formatContacts($representative, $contacts)
    {
        if (1 === count($contacts)) {
            $representative->phoneTop1 = $contacts[0]['value'];
            $representative->phoneFoolter = $contacts[0]['value'];

            return $representative;
        }

        $matches = [];
        foreach ($contacts as $index => $contact) {
            $matches[$index] = [];
            if (!preg_match('/\((\d+)\)\s*(.+)/uD', $contact['value'], $matches[$index])) {
                $matches[$index][1] = '';
                $matches[$index][2] = '';    
            }
        }

        if (2 === count($contacts)) {
            $representative->phoneTop1 = $contacts[0]['value'];
            $representative->phoneTop2 = $contacts[1]['value'];
            $representative->phoneFoolter = $contacts[0]['value'].', ';
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phoneFoolter = $matches[1][2];
            }
            else {
                $representative->phoneFoolter = $contacts[1]['value'];
            }

            return $representative;
        }
        
        if ($matches[0][1]) {
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phoneTop1 = '('.$matches[0][1].') '.$matches[0][2].', '.$matches[1][2];
                $representative->phoneTop2 = $contacts[2]['value'];
            }
            elseif ($matches[0][1] == $matches[2][1]) {
                $representative->phoneTop1 = '('.$matches[0][1].') '.$matches[0][2].', '.$matches[2][2];
                $representative->phoneTop2 = $contacts[1]['value'];

            }
            else {
                $representative->phoneTop1 = $contacts[0]['value'];
                $representative->phoneTop2 = $contacts[1]['value'];
            }
        }
        elseif ($matches[1][1] && $matches[1][1] == $matches[2][1]) {
            $representative->phoneTop = '('.$matches[1][1].') '.$matches[1][2].', '.$matches[2][2];
            $representative->phoneTop2 = $contacts[0]['value'];
        }

        return $representative;
    }

    protected function formatSchedule($representative, $schedule)
    {
        if (!$schedule['s']) {
            $representative->schedule = 'выходной';
        }
        elseif (0 === $schedule['s'] % 3600 && 0 === $schedule['t'] % 36000) {
            $representative->schedule = date('G', $schedule['s']).' - '.date('G', $schedule['t']);
        }
        else {
            $representative->schedule = date('G:i', $schedule['s']).' - '.date('G:i', $schedule['t']);
        }

        return $representative;
    }
}
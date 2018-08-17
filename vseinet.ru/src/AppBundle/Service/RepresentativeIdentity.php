<?php 

namespace AppBundle\Service;

use AppBundle\Container\ContainerAware;
use AppBundle\Entity\Representative;
use AppBundle\Enum\ContactTypeCode;

class RepresentativeIdentity extends ContainerAware
{
    public function getRepresentative(): Representative
    {
        $session = $this->get('request_stack')->getMasterRequest()->getSession();

        $geoCity = $this->getGeoCity();
        if (0 === $geoCity->getCountGeoPoints()) {
            $geoCityId = $this->getParameter('default.geo_city_id');
        } else {
            $geoCityId = $geoCity->getId();
        }

        $representative = $session->get('representative');
        if (null === $representative || $representative->geoCityId !== $geoCityId) {
            $representative = $this->loadRepresentative($geoCityId);
            $session->set('representative', $representative);
        }

        return $representative;
    }

    protected function loadRepresentative(int $geoCityId): Representative
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT r
            FROM AppBundle:Representative AS r 
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId 
            INNER JOIN AppBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
            WHERE r.isActive = true AND gc.id = :geoCityId AND r.isCentral = true
        ");
        $q->setParameter('geoCityId', $geoCityId);
        $representative = $q->getSingleResult();
        if (!$representative instanceof Representative) {
            throw new \RuntimeException('Representative not found');
        }

        $q = $em->createQuery("
            SELECT
                c.value,
                CASE WHEN c.isMain = true THEN 1 ELSE CASE WHEN c.contactTypeCode = :phoneOrd THEN 2 ELSE 3 END END AS ORD
            FROM AppBundle:RepresentativePhone AS rp 
            INNER JOIN AppBundle:Contact AS c WITH c.id = rp.contactId
            WHERE rp.representativeId = :representativeId AND c.contactTypeCode IN (:phone, :mobile)
            ORDER BY ORD 
        ");
        $q->setParameter('representativeId', $representative->getGeoPointId());
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
            FROM AppBundle:RepresentativeSchedule rs 
            WHERE rs.representativeId = :representativeId     
        ");
        $q->setParameter('representativeId', $representative->getGeoPointId());
        $schedule = $q->getSingleResult();
        $this->formatSchedule($representative, $schedule);

        $representative->geoCityId = $geoCityId;

        return $representative;
    }

    protected function formatContacts(Representative $representative, array $contacts): Representative
    {
        if (1 === count($contacts)) {
            $representative->phoneTop1 = $contacts[0]['value'];
            $representative->phoneFoolter = $contacts[0]['value'];

            return $representative;
        }

        foreach ($contacts as &$contact) {
            $contact['value'] = $this->get('phone.formatter')->format($contact['value']);
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
        }

        if (2 === count($contacts)) {
            $representative->phone1 = $contacts[0]['value'];
            $representative->phone2 = $contacts[1]['value'];
            $representative->phone3 = $contacts[0]['value'].', ';
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phone3 = $matches[1][2];
            }
            else {
                $representative->phone3 = $contacts[1]['value'];
            }

            return $representative;
        }
        
        if ($matches[0][1]) {
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phone1 = '+7 ('.$matches[0][1].') '.$matches[0][2].', '.$matches[1][2];
                $representative->phone2 = $contacts[2]['value'];
                if ($matches[0][1] == $matches[2][1]) {
                    $representative->phone3 = $representative->phone1.', '.$matches[2][2];    
                }
            }
            elseif ($matches[0][1] == $matches[2][1]) {
                $representative->phone1 = '+7 ('.$matches[0][1].') '.$matches[0][2].', '.$matches[2][2];
                $representative->phone2 = $contacts[1]['value'];

            }
            else {
                $representative->phone1 = $contacts[0]['value'];
                $representative->phone2 = $contacts[1]['value'];
            }
        }
        elseif ($matches[1][1] && $matches[1][1] == $matches[2][1]) {
            $representative->phone1 = '+7 ('.$matches[1][1].') '.$matches[1][2].', '.$matches[2][2];
            $representative->phone2 = $contacts[0]['value'];
        }
        if (null === $representative->phone3) {
            $representative->phone3 = $representative->phone1.', '.$representative->phone2;
        }

        return $representative;
    }

    protected function formatSchedule(Representative $representative, array $schedule): Representative
    {
        if (!$schedule['s']) {
            $representative->schedule = 'выходной';
        }
        elseif (0 === $schedule['s']->getTimestamp() % 3600 && 0 === $schedule['t']->getTimestamp() % 3600) {
            $representative->schedule = $schedule['s']->format('G').' - '.$schedule['t']->format('G');
        }
        else {
            $representative->schedule = $schedule['s']->format('G:i').' - '.$schedule['t']->format('G:i');
        }

        return $representative;
    }
}

<?php

namespace AppBundle\Service;

use AppBundle\Container\ContainerAware;
use AppBundle\Entity\Representative;
use AppBundle\Enum\ContactTypeCode;

class RepresentativeIdentity extends ContainerAware
{
    public function getEmployeeRepresentative(): ?Representative
    {
        $em = $this->getDoctrine()->getManager();
        $token = $this->get('security.token_storage')->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user) && $user->isEmployee()) {
                $session = $this->get('request_stack')->getMasterRequest()->getSession();
                $representative = $session->get('employeeRepresentative');
                if (null === $representative) {
                    $representative = $em->createQuery(/* @lang DQL */'
                        SELECT r
                        FROM AppBundle:EmployeeToGeoRoom AS oetgr
                        JOIN AppBundle:GeoRoom AS gr WITH gr.id = oetgr.geoRoomId
                        JOIN AppBundle:Representative AS r WITH r.geoPointId = gr.geoPointId
                        WHERE oetgr.employeeId = :userId AND oetgr.isMain = true
                    ')
                    ->setParameter('userId', $user->getId())
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
                    if (!$representative) {
                        $representative = $em->getRepository(Representative::class)->find($this->container->getParameter('default.point.id'));
                    }
                    $session->set('employeeRepresentative', $representative);
                }

                return $representative;
            }
        }

        return null;
    }

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
        if (null === $representative || $representative->getGeoCityId() !== $geoCityId) {
            $representative = $this->loadRepresentative($geoCityId);
            $session->set('representative', $representative);
        }

        return $representative;
    }

    protected function loadRepresentative(int $geoCityId): Representative
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT r,
                CASE WHEN gp.geoCityId = gc.id THEN 0 ELSE 1 END HIDDEN ORD
            FROM AppBundle:GeoCity AS gc
            JOIN AppBundle:GeoCity AS ogc WITH gc.geoRegionId = ogc.geoRegionId
            JOIN AppBundle:GeoPoint AS gp WITH gp.geoCityId = ogc.id
            JOIN AppBundle:Representative AS r WITH gp.id = r.geoPointId
            WHERE r.isActive = true AND gc.id = :geoCityId
            ORDER BY ORD, r.isCentral DESC
        ");
        $q->setParameter('geoCityId', $geoCityId);
        $q->setMaxResults(1);
        $representative = $q->getSingleResult();
        if (!$representative instanceof Representative) {
            throw new \RuntimeException('Представительство не найдено');
        }

        $q = $em->createQuery("
            SELECT ga.address
            FROM AppBundle:GeoAddress AS ga
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.geoAddressId = ga.id
            WHERE gp.id = :id
        ");
        $q->setParameter('id', $representative->getGeoPointId());
        try {
            $representative->address = $q->getSingleScalarResult();
        } catch (\Exception $e) {
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

        return $representative;
    }

    protected function formatContacts(Representative $representative, array $contacts): Representative
    {
        foreach ($contacts as $index => $contact) {
            $contacts[$index]['value'] = $this->get('phone.formatter')->format($contact['value']);
        }

        if (1 === count($contacts)) {
            $representative->phone1 = $contacts[0]['value'];
            $representative->phone3 = $contacts[0]['value'];

            return $representative;
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
            $representative->contacts[] = $contact['value'];
        }

        if (2 === count($contacts)) {
            $representative->phone1 = $contacts[0]['value'];
            $representative->phone2 = $contacts[1]['value'];
            $representative->phone3 = $contacts[0]['value'].', ';
            if ($matches[0][1] == $matches[1][1]) {
                $representative->phone3 .= $matches[1][2];
            }
            else {
                $representative->phone3 .= $contacts[1]['value'];
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
            $representative->schedule = $schedule['s']->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('G').' - '.$schedule['t']->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('G');
        }
        else {
            $representative->schedule = $schedule['s']->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('G:i').' - '.$schedule['t']->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('G:i');
        }

        return $representative;
    }
}

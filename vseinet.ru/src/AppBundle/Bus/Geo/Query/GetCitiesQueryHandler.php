<?php

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\GeoRegion;

class GetCitiesQueryHandler extends MessageHandler
{
    public function handle(GetCitiesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $geoRegion = $em->getRepository(GeoRegion::class)->find($query->geoRegionId);
        if (!$geoRegion instanceof GeoRegion) {
            throw new NotFoundHttpException('Регион не найден');
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Geo\Query\DTO\City (
                    gc.id,
                    gc.name,
                    gc.isCentral
                )
            FROM AppBundle:GeoCity AS gc
            WHERE gc.geoRegionId = :geoRegionId AND gc.AOLEVEL <= 4 AND gc.geoAreaId IS NULL
            ORDER BY gc.name
        ");
        $q->setParameter('geoRegionId', $query->geoRegionId);
        $geoCities = $q->getResult('IndexByHydrator');

        if (empty($geoCities)) {
            return [
                'geoCityCentral' => null,
                'geoCityGroups' => [],
            ];
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Geo\Query\DTO\Point (
                    gp.geoCityId,
                    r.hasRetail,
                    r.hasDelivery,
                    CASE WHEN TIMESTAMPDIFF(MONTH, r.openingDate, CURRENT_TIMESTAMP()) < 2 THEN true ELSE false END
                )
            FROM AppBundle:GeoPoint AS gp
            INNER JOIN AppBundle:Representative AS r WITH r.geoPointId = gp.id
            WHERE gp.geoCityId IN (:geoCityIds) AND r.isActive = true
        ");
        $q->setParameter('geoCityIds', array_keys($geoCities));
        $geoPoints = $q->getArrayResult();

        foreach ($geoPoints as $geoPoint) {
            if ($geoPoint->hasRetail) {
                $geoCities[$geoPoint->geoCityId]->hasRetail = true;
            }
            if ($geoPoint->hasDelivery) {
                $geoCities[$geoPoint->geoCityId]->hasDelivery = true;
            }
            if ($geoPoint->isNew) {
                $geoCities[$geoPoint->geoCityId]->countNewPoints += 1;
            }
        }

        $geoCityId = $this->getGeoCity()->getId();

        $geoCityCentral = null;
        $alphabetIndex = [];
        foreach ($geoCities as $geoCity) {
            $geoCity->isCurrent = $geoCity->id === $geoCityId;
            if ($geoCity->isCentral) {
                $geoCityCentral = $geoCity;
            }
            $alphabetIndex[mb_substr($geoCity->name, 0, 1)][] = $geoCity;
        }

        $groupCount = 3;
        $getCityPerGroup = ceil(count($geoCities) / $groupCount);
        $geoCityGroups = [];
        $counter = 0;
        $index = 0;
        foreach ($alphabetIndex as $letter => $geoCities) {
            $counter += count($geoCities);
            $geoCityGroups[$index][$letter] = $geoCities;
            if ($counter >= $getCityPerGroup) {
                $counter = 0;
                $index++;
            }
        }

        return [
            'geoCityCentral' => $geoCityCentral,
            'geoCityGroups' => $geoCityGroups,
        ];
    }
}

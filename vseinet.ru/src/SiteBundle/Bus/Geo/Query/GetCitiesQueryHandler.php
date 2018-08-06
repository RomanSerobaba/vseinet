<?php 

namespace SiteBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GeoBundle\Entity\GeoRegion;

class GetCitiesQueryHandler extends MessageHandler
{
    public function handle(GetCitiesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $region = $em->getRepository(GeoRegion::class)->find($query->regionId);
        if (!$region instanceof GeoRegion) {
            throw new NotFoundHttpException('Регион не найден');
        }

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Geo\Query\DTO\City (
                    gc.id,
                    gc.name,
                    gc.isCentral
                )
            FROM GeoBundle:GeoCity gc 
            WHERE gs.geoRegionId = :regionId 
            ORDER BY gr.name 
        ");
        $cities = $q->getResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT
                NEW SiteBundle\Bus\Geo\Query\DTO\Point (
                    gp.geoCityId,
                    r.hasRetail,
                    r.hasDelivery,
                    CASE WHEN TIMESTAMPDIFF(MONTH, r.openingDate, NOW()) < 2 THEN true ELSE false END 
                )
            FROM GeoBundle:GetPoint gp
            INNER JOIN OrgBundle:Representative r WITH r.geoPointId = gp.id 
            WHERE gp.geoCityId IN (:cityIds) AND r.isActive = true 
        ");
        $q->setParameter('cityIds', array_keys($cities));
        $points = $q->getArrayResult();

        foreach ($points as $point) {
            if ($point->hasRetail) {
                $cities[$point->geoCityId]->hasRetail = true;
            }
            if ($point->hasDelivery) {
                $cities[$point->geoCityId]->hasDelivery = true;
            }
            if ($point->isNew) {
                $cities[$point->geoCityId]->countNewPoints += 1;
            }
        }

        $geoCityId = $this->get('session', 'geo_city_id') ?: $this->getParameter('default.city.id');

        $cityCentral = null;
        $alphabetIndex = [];
        foreach ($cities as $city) {
            $city->isCurrent = $city->id === $geoCityId;
            if (preg_match('~^(п\.|пос\.)\s*(.+)~uD', $city->name, $matches)) {
                $city->name = $matches[2].' '.$matches[1];
            }
            if ($city->isCentral) {
                $cityCentral = $city;
            }
            $alphabetIndex[mb_substr($city->name, 0, 1)][] = $city;
        }
        
        $groupCount = 3;
        $cityPerGroup = ceil(count($cities) / $groupCount);
        $cityGroups = [];
        $counter = 0;
        $index = 0;
        foreach ($alphabetIndex as $letter => $cities) {
            $counter += count($cities);
            $cityGroups[$index][$letter] = $cities;
            if ($counter >= $cityPerGroup) {
                $counter = 0;
                $index++;
            }
        }

        return [
            'cityCentral' => $cityCentral,
            'cityGroups' => $cityGroups,
        ];
    }
}
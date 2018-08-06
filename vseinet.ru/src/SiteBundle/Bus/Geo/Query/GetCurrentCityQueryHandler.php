<?php 

namespace SiteBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GeoBundle\Entity\GeoRegion;

class GetCurrentCityQueryHandler extends MessageHandler
{
    public function handle(GetCurrentCityQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $geoCityId = $this->get('session')->get('geo_city_id');
        if (null === $geoCityId) {

            $ip = $this->get('request_stack')->getCurrentRequest()->getClientIp();
            
            $q = $em->createQuery("
                SELECT 
                    gi.geoCityId 
                FROM GeoBundle:GeoIp gi 
                WHERE :longIp BETWEEN gi.longIp1 AND gi.longIp2 
            ");
            $q->setParameter('longIp', ip2long($ip));
            $q->setMaxResults(1);
            $geoCityId = $q->getOneOrNullResult() ?: 0;
            
            $this->get('session')->set('geo_city_id', $geoCityId);
        }

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Geo\Query\DTO\City (
                    gc.id,
                    gc.name,
                    gc.isCentral
                )
            FROM GeoBundle:GeoCity gc 
            WHERE gc.id = :geoCityId 
        ");
        $q->setParameter('geoCityId', $geoCityId ?: $this->getParameter('default.city.id'));
        $city = $q->getSingleResult();

        $q = $em->createQuery("
            SELECT
                NEW SiteBundle\Bus\Geo\Query\DTO\Point (
                    gp.geoCityId,
                    r.hasRetail,
                    r.hasDelivery,
                    CASE WHEN TIMESTAMPDIFF(MONTH, r.openingDate, CURRENT_DATE()) < 2 THEN true ELSE false END 
                )
            FROM GeoBundle:GeoPoint gp
            INNER JOIN OrgBundle:Representative r WITH r.geoPointId = gp.id 
            WHERE gp.geoCityId = :geoCityId AND r.isActive = true 
        ");
        $q->setParameter('geoCityId', $city->id);
        $points = $q->getArrayResult();

        foreach ($points as $point) {
            if ($point->hasRetail) {
                $city->hasRetail = true;
            }
            if ($point->hasDelivery) {
                $city->hasDelivery = true;
            }
            if ($point->isNew) {
                $city->countNewPoints += 1;
            }
        }
        $city->isCurrent = true;

        return $city;
    }
}
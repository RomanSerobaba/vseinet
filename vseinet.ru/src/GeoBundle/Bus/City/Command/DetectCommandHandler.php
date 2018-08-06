<?php 

namespace GeoBundle\Bus\City\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ContactTypeCode;

class DetectCommandHandler extends MessageHandler
{
    public function handle(DetectCommand $command)
    {
        $geoCityId = $this->get('session')->get('geo_city_id');
        if (null === $geoCityId) {

            $ip = $this->get('request')->getClientIp();
            
            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT gi.geoCityId 
                FROM GeoBundle:GeoIp gi 
                WHERE gi.longIp1 <= :ip1 AND gi.longIp2 >= :ip2 
            ");
            $q->setParameter('ip1', ip2long($ip));
            $q->setParameter('ip2', ip2long($ip));
            $q->setMaxResults(1);
            
            $geoCityId = $q->getOneOrNullResult() ?: 0; // город не определился
            
            $this->get('session')->set('geo_city_id', $geoCityId);
        }
    }
}

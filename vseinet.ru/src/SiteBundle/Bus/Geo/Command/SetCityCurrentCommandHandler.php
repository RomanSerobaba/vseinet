<?php 

namespace SiteBundle\Bus\Geo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GeoBundle\Entity\GeoCity;

class SetCityCurrentCommandHandler extends MessageHandler
{
    public function handle(SetCityCurrentCommand $command)
    {
        $city = $this->getDoctrine()->getManager()->getRepository(GeoCity::class)->find($command->id);
        if (!$city instanceof GeoCity) {
            throw new NotFoundHttpException('Город не найден');
        }

        $this->get('session')->set('geo_city_id', $city->getId());
    }
}
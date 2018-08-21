<?php 

namespace AppBundle\Bus\Geo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\GeoCity;

class SetCityCurrentCommandHandler extends MessageHandler
{
    public function handle(SetCityCurrentCommand $command)
    {
        $geoCity = $this->getDoctrine()->getManager()->getRepository(GeoCity::class)->find($command->id);
        if (!$geoCity instanceof GeoCity) {
            throw new NotFoundHttpException('Город не найден');
        }

        $this->get('geo_city.identity')->setGeoCityId($geoCity->getId());
    }
}

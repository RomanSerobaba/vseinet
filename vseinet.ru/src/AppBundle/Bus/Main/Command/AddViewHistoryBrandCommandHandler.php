<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class AddViewHistoryBrandCommandHandler extends MessageHandler
{
    public function handle(AddViewHistoryBrandCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $request = $this->get('request_stack')->getMasterRequest();

        if (!$this->getUserIsEmployee()) {
            $q = $em->createNativeQuery('
                INSERT INTO view_history_brand
                (brand_id, geo_city_id, user_id, viewed_at, ip)

                VALUES(:brand_id, :geo_city_id, :user_id, :viewed_at, :ip)
            ', new ResultSetMapping());
            $q->setParameter('brand_id', $command->brandId);
            $q->setParameter('geo_city_id', $this->getGeoCity()->getRealId());
            $q->setParameter('user_id', $user ? $user->getId() : null);
            $q->setParameter('viewed_at', new \DateTime());
            $q->setParameter('ip', $request->getClientIp());
            $q->execute();
        }
    }
}

<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class AddViewHistoryCategoryCommandHandler extends MessageHandler
{
    public const LIMIT = 6;

    public function handle(AddViewHistoryCategoryCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $request = $this->get('request_stack')->getMasterRequest();

        if (!$this->getUserIsEmployee()) {
            $q = $em->createNativeQuery('
                INSERT INTO view_history_category
                (category_id, geo_city_id, user_id, viewed_at, ip)

                VALUES(:category_id, :geo_city_id, :user_id, :viewed_at, :ip)
            ', new ResultSetMapping());
            $q->setParameter('category_id', $command->categoryId);
            $q->setParameter('geo_city_id', $this->getGeoCity()->getRealId());
            $q->setParameter('user_id', $user ? $user->getId() : null);
            $q->setParameter('viewed_at', new \DateTime());
            $q->setParameter('ip', $request->getClientIp());
            $q->execute();
        }
    }
}

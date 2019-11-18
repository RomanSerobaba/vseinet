<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class AddViewHistoryProductCommandHandler extends MessageHandler
{
    public const LIMIT = 6;

    public function handle(AddViewHistoryProductCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $request = $this->get('request_stack')->getMasterRequest();

        if (!$this->getUserIsEmployee()) {
            $q = $em->createNativeQuery('
                INSERT INTO view_history_product
                (base_product_id, geo_city_id, user_id, viewed_at, ip)

                VALUES(:base_product_id, :geo_city_id, :user_id, :viewed_at, :ip)
                SELECT oi.id, :expires_at
                FROM order_item AS oi
                WHERE oi.order_did = :id
            ', new ResultSetMapping());
            $q->setParameter('base_product_id', $command->baseProductId);
            $q->setParameter('geo_city_id', $this->getGeoCity()->getRealId());
            $q->setParameter('user_id', $user ? $user->getId() : null);
            $q->setParameter('viewed_at', new \DateTime());
            $q->setParameter('ip', $request->getClientIp());
            $q->execute();
        }
    }
}

<?php

namespace SiteBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetInfoQueryHandler extends MessageHandler
{
    public function handle(GetInfoQuery $query)
    {
        if ($this->get('user.identity')->isAuthorized()) {
            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT f.baseProductId, f.baseProductId
                FROM SiteBundle:Favorite f 
                WHERE f.userId = :userId 
            ");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
            $ids = $q->getResult('ListHydrator');
        } else {
            $ids = $this->get('session')->get('favorites', []);
        }

        return new DTO\Info($ids);
    }
}

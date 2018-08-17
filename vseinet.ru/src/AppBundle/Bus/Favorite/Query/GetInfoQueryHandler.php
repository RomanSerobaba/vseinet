<?php

namespace AppBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetInfoQueryHandler extends MessageHandler
{
    public function handle(GetInfoQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT f.baseProductId, f.baseProductId
                FROM AppBundle:Favorite f 
                WHERE f.userId = :userId 
            ");
            $q->setParameter('userId', $user->getId());
            $ids = $q->getResult('ListHydrator');
        } else {
            $ids = $this->get('session')->get('favorites', []);
        }

        return new DTO\Info($ids);
    }
}

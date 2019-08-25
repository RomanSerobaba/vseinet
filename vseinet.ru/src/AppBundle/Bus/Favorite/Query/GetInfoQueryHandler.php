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
                SELECT bp.canonicalId, bp.canonicalId
                FROM AppBundle:Favorite f 
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = f.baseProductId
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

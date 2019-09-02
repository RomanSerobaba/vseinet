<?php

namespace AppBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetProductIdsQueryHandler extends MessageHandler
{
    public function handle(GetProductIdsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT bp.canonicalId AS baseProductId
                FROM AppBundle:Favorite f
                JOIN AppBundle:BaseProduct AS bp WITH bp.id = f.baseProductId
                WHERE f.userId = :userId
            ");
            $q->setParameter('userId', $user->getId());

            return $q->getResult('ListHydrator');
        }

        return $this->get('session')->get('favorites', []);
    }
}

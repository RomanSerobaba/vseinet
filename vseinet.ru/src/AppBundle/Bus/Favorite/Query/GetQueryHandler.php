<?php

namespace AppBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT
                    f.baseProductId
                FROM AppBundle:Favorite f
                WHERE f.userId = :userId
            ");
            $q->setParameter('userId', $user->getId());
            $ids = $q->getResult('ListHydrator');
        } else {
            $ids = $this->get('session')->get('favorites', []);
        }

        if (empty($ids)) {
            return [];
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Favorite\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    COALESCE(p.price, p2.price),
                    bpi.basename
                )
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            WHERE bp.id IN (:ids) AND p2.geoCityId = 0
            ORDER BY bp.name
        ");
        $q->setParameter('ids', $ids);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());

        return $q->getArrayResult();
    }
}

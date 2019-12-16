<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetBlockLastviewQueryHandler extends MessageHandler
{
    public function handle(GetBlockLastviewQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT bp.canonicalId AS baseProductId
                FROM AppBundle:BaseProductLastview AS bplv
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = bplv.baseProductId
                WHERE bplv.userId = :userId
                ORDER BY bplv.viewedAt ASC
            ");
            $q->setParameter('userId', $user->getId());
            $productIds = $q->getResult('ListHydrator');
        } else {
            $request = $this->get('request_stack')->getMasterRequest();
            $productIdsStr = $request->cookies->get('products_lastview', '');
            $productIds = empty($productIdsStr) ? [] : array_reverse(array_filter(array_map('intval', explode(',', $productIdsStr))));
        }
        if (empty($productIds)) {
            return [];
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Main\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    c.name,
                    COALESCE(p.price, p2.price),
                    bpi.basename
                )
            FROM AppBundle:BaseProduct AS bp
            LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND bpi.width > 0
            LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.canonicalId AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.canonicalId
            INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId
            INNER JOIN AppBundle:Category AS c WITH c.id = cp.id
            WHERE bp.id IN (:ids) AND p2.geoCityId = 0
            GROUP BY bp.id, c.id, p.price, p2.price, bpi.id
        ");
        $q->setParameter('ids', $productIds);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setMaxResults($query->count);
        $products = $q->getResult('IndexByHydrator');

        $sorted = [];
        foreach ($productIds as $id) {
            if (isset($products[$id])) {
                $sorted[] = $products[$id];
            }
        }

        return $sorted;
    }
}

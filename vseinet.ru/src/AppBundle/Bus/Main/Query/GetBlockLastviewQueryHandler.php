<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockLastviewQueryHandler extends MessageHandler
{
    public function handle(GetBlockLastviewQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT bplv.baseProductId
                FROM AppBundle:BaseProductLastview AS bplv
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
            LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            LEFT OUTER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.id
            INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId
            INNER JOIN AppBundle:Category AS c WITH c.id = cp.id
            WHERE bp.id IN (:ids) AND p2.geoCityId = 0
            GROUP BY bp.id, c.id, p.price, p2.price, bpi.id
        ");
        $q->setParameter('ids', $productIds);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setMaxResults($query->count);
        $products = $q->getArrayResult('IndexByHydrator');

        $sorted = [];
        foreach ($productIds as $id) {
            if (isset($products[$id])) {
                $sorted[] = $products[$id];
            }
        }

        return $sorted;
    }
}

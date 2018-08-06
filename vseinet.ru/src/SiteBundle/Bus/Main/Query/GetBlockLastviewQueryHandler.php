<?php

namespace SiteBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockLastviewQueryHandler extends MessageHandler
{
    public function handle(GetBlockLastviewQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('user.identity')->isAuthorized()) {
            $q = $em->createQuery("
                SELECT bplv.baseProductId 
                FROM SiteBundle:BaseProductLastview AS bplv
                WHERE bplv.userId = :userId 
                ORDER BY bplv.viewedAt ASC 
            ");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
            $productIds = $q->getResult('ListHydrator');
        } else {
            $request = $this->get('request_stack')->getMasterRequest();
            $productIdsStr = $request->cookies->get('products_lastview', '');
            $productIds = empty($productIdsStr) ? [] : array_reverse(array_filter(array_map('intval', explode(',', $productIdsStr))));
        }
        if (empty($productIds)) {
            return [];
        }

        $cityId = $this->get('city.identity')->getId();
        $criteria = $cityId ? "p.geoCityId = {$cityId}" : "p.geoCityId IS NULL";
        $ord = '';
        foreach ($productIds as $index => $id) {
            $ord .= " WHEN bp.id = {$id} THEN {$index}";
        }

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Main\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    c.name,
                    p.price,
                    bpi.basename 
                ),
                CASE {$ord} ELSE 0 END AS HIDDEN ORD 
            FROM ContentBundle:BaseProduct AS bp 
            LEFT OUTER JOIN ContentBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            INNER JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id 
            INNER JOIN ContentBundle:CategoryPath AS cp WITH cp.id = bp.categoryId 
            INNER JOIN ContentBundle:Category AS c WITH c.id = cp.id
            WHERE bp.id IN (:ids) AND {$criteria}
            GROUP BY bp.id, c.id, p.id, bpi.id
            ORDER BY ORD
        ");
        $q->setParameter('ids', $productIds);
        $q->setMaxResults(6);
        $products = $q->getArrayResult();

        return $products;
    }
}

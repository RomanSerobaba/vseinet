<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockRelatedQueryHandler extends MessageHandler
{
    public function handle(GetBlockRelatedQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Main\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    c.name,
                    COALESCE(p.price, p0.price),
                    FIRST(
                        SELECT
                            bpi.basename
                        FROM AppBundle:BaseProductImage AS bpi
                        WHERE bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND bpi.width > 0
                    ),
                    bp.sefUrl
                ),
                MAX(o.createdAt) AS HIDDEN ORD,
                COUNT(o.DId) AS HIDDEN ORD2
            FROM AppBundle:BaseProduct AS bp1
            INNER JOIN AppBundle:OrderItem AS oi WITH oi.baseProductId = bp1.id
            INNER JOIN AppBundle:OrderDoc AS o WITH o.DId = oi.orderDid
            INNER JOIN AppBundle:OrderItem AS oi2 WITH oi.orderDid = oi2.orderDid
            INNER JOIN AppBundle:BaseProduct AS bp2 WITH bp2.id = oi2.baseProductId
            INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = bp2.canonicalId
            LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.canonicalId AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p0 WITH p0.baseProductId = bp.canonicalId
            INNER JOIN AppBundle:Category AS c WITH c.id = bp.categoryId
            WHERE bp1.canonicalId = :id AND bp2.canonicalId != :id AND p0.geoCityId = 0 AND o.orderTypeCode IN (:orderTypeCodes) AND p0.productAvailabilityCode > :productAvailabilityCode_OUT_OF_STOCK
            GROUP BY bp.id, c.id, p.price, p0.price
            ORDER BY ORD2 DESC, ORD DESC
        ");
        $q->setParameter('id', $query->baseProductId);
        $q->setParameter('orderTypeCodes', [OrderTypeCode::SITE, OrderTypeCode::SHOP, OrderTypeCode::LEGAL, OrderTypeCode::REQUEST]);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setParameter('productAvailabilityCode_OUT_OF_STOCK', ProductAvailabilityCode::OUT_OF_STOCK);
        $q->setMaxResults($query->count);
        $products = $q->getResult('IndexByHydrator');

        return $products;
    }
}

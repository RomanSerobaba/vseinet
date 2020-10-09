<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockSimilarQueryHandler extends MessageHandler
{
    public function handle(GetBlockSimilarQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Main\Query\DTO\Product (
                    bp2.id,
                    bp2.name,
                    bp2.categoryId,
                    c.name,
                    COALESCE(p2.price, p02.price),
                    FIRST(
                        SELECT
                            bpi.basename
                        FROM AppBundle:BaseProductImage AS bpi
                        WHERE bpi.baseProductId = bp2.id AND bpi.sortOrder = 1 AND bpi.width > 0
                    ),
                    bp2.sefUrl
                ),
                ABS(COALESCE(p1.price, p01.price) - COALESCE(p2.price, p02.price)) AS HIDDEN ORD
            FROM AppBundle:BaseProduct AS bp1
            LEFT JOIN AppBundle:Product AS p1 WITH p1.baseProductId = bp1.id AND p1.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p01 WITH p01.baseProductId = bp1.id
            INNER JOIN AppBundle:BaseProduct AS bp2 WITH bp1.categoryId = bp2.categoryId AND bp2.id != bp1.id
            LEFT JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp2.id AND p2.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p02 WITH p02.baseProductId = bp2.id
            INNER JOIN AppBundle:Category AS c WITH c.id = bp2.categoryId
            WHERE bp1.id = :id AND p01.geoCityId = 0 AND p02.geoCityId = 0 AND p02.productAvailabilityCode > :productAvailabilityCode_OUT_OF_STOCK AND bp2.id = bp2.canonicalId
            GROUP BY bp2.id, bp2.state, c.id, p2.price, p02.price, p1.price, p01.price
            HAVING FIRST(
                SELECT
                    1
                FROM AppBundle:BaseProductImage AS bpi2
                WHERE bpi2.baseProductId = bp2.id AND bpi2.sortOrder = 1 AND bpi2.width > 0
            ) = 1
            ORDER BY ORD
        ");
        $q->setParameter('id', $query->baseProductId);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setParameter('productAvailabilityCode_OUT_OF_STOCK', ProductAvailabilityCode::OUT_OF_STOCK);
        $q->setMaxResults($query->count);
        $products = $q->getResult('IndexByHydrator');

        return $products;
    }
}

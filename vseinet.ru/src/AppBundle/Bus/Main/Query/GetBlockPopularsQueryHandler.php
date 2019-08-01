<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockPopularsQueryHandler extends MessageHandler
{
    public function handle(GetBlockPopularsQuery $query)
    {
        $products = [];

        $cache = $this->get('cache.provider.memcached');
        $cachedBlock = $cache->getItem('block_populars_'.$this->getGeoCity()->getRealId());
        if ($cachedBlock->isHit()) {
            foreach ($cachedBlock->get() as $id) {
                $cachedProduct = $cache->getItem('block_populars_product_'.$id);
                if ($cachedProduct->isHit()) {
                    $products[$id] = $cachedProduct->get();
                }
            }
        }

        if (0 === ($query->count -= count($products))) {
            return $products;
        }

        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT MIN(bp.id), MAX(bp.id)
            FROM AppBundle:BaseProduct AS bp
        ');
        $baseProductIds = $q->getSingleResult();

        $categoryIds = array_merge([0], array_map(function ($product) { return $product->categoryId; }, $products));

        while ($query->count--) {
            $randomId = rand($baseProductIds[2], $baseProductIds[1]);
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Main\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        c.name,
                         (
                            SELECT COALESCE(p.price, p0.price)
                            FROM AppBundle:Product AS p0
                            WHERE p0.baseProductId = bp.id AND p0.geoCityId = 0 AND p0.productAvailabilityCode = :on_demand AND p0.price > 0
                        ),
                        bpi.basename
                    )
                FROM AppBundle:BaseProduct AS bp
                INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId AND p.productAvailabilityCode = :available AND p.price > 0
                INNER JOIN AppBundle:Category AS c WITH c.id = bp.categoryId
                WHERE bp.id >= :randomId AND bp.categoryId NOT IN (:categoryIds)
                GROUP BY bp.id, c.id, bpi.id, p.price, p.baseProductId
                HAVING p.baseProductId IS NOT NULL OR FIRST(
                    SELECT r.geoPointId
                    FROM AppBundle:GeoPoint AS gp
                    JOIN AppBundle:Representative AS r WITH r.geoPointId = gp.id
                    WHERE gp.geoCityId = :geoCityId AND r.isActive = true
                ) IS NULL
            ");
            $q->setParameter('randomId', $randomId);
            $q->setParameter('categoryIds', $categoryIds);
            $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
            $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
            $q->setParameter('on_demand', ProductAvailabilityCode::ON_DEMAND);
            $q->setMaxResults(1);
            try {
                $product = $q->getSingleResult();
                $products[$product->id] = $product;
                $categoryIds[] = $product->categoryId;

                $cachedProduct = $cache->getItem('block_populars_product_'.$product->id);
                $cachedProduct->set($product);
                $cachedProduct->expiresAfter(300 + rand(0, 100));
                $cache->save($cachedProduct);
            } catch (\Exception $e) {
            }
        }

        $cachedBlock->set(array_keys($products));
        $cachedBlock->expiresAfter(300 + rand(0, 100));
        $cache->save($cachedBlock);

        return $products;
    }
}

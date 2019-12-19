<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;
use Doctrine\ORM\AbstractQuery;

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
            WHERE bp.id = bp.canonicalId
        ');
        $baseProductIds = $q->getSingleResult();

        $categoryIds = array_merge([0], array_map(function ($product) { return $product->categoryId; }, $products));

        while ($query->count--) {
            $randomId = rand($baseProductIds[2], $baseProductIds[1]);
            $q = $em->createQuery('
                SELECT r.geoPointId
                FROM AppBundle:Representative AS r
                JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
                WHERE gp.geoCityId = :geoCityId AND r.isActive = TRUE AND r.isCentral = TRUE
            ')->setParameter('geoCityId', $this->getGeoCity()->getRealId());
            $geoPointId = $q->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

            if ($geoPointId) {
                $q = $em->createQuery("
                    SELECT
                        NEW AppBundle\Bus\Main\Query\DTO\Product (
                            bp.id,
                            bp.name,
                            bp.categoryId,
                            c.name,
                            p.price,
                            bpi.basename,
                            bp.chpuName
                        )
                    FROM AppBundle:BaseProduct AS bp
                    INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND bpi.width > 0
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.canonicalId AND p.geoCityId = :geoCityId AND p.productAvailabilityCode = :available AND p.price > 0
                    INNER JOIN AppBundle:Category AS c WITH c.id = bp.categoryId
                    WHERE bp.id >= :randomId AND bp.categoryId NOT IN (:categoryIds) AND bp.id = bp.canonicalId
                ")
                    ->setParameters([
                        'randomId' => $randomId,
                        'categoryIds' => $categoryIds,
                        'geoCityId' => $this->getGeoCity()->getRealId(),
                        'available' => ProductAvailabilityCode::AVAILABLE
                    ]);

            } else {
                $q = $em->createQuery("
                    SELECT
                        NEW AppBundle\Bus\Main\Query\DTO\Product (
                            bp.id,
                            bp.name,
                            bp.categoryId,
                            c.name,
                            p.price,
                            bpi.basename
                        )
                    FROM AppBundle:BaseProduct AS bp
                    INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND bpi.width > 0
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = 0 AND p.productAvailabilityCode = :on_demand AND p.price > 0
                    INNER JOIN AppBundle:Category AS c WITH c.id = bp.categoryId
                    WHERE bp.id >= :randomId AND bp.categoryId NOT IN (:categoryIds) AND bp.id = bp.canonicalId
                ")
                    ->setParameters([
                        'randomId' => $randomId,
                        'categoryIds' => $categoryIds,
                        'on_demand' => ProductAvailabilityCode::ON_DEMAND
                    ]);
            }

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

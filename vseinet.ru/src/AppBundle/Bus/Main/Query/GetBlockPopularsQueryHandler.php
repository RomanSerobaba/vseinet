<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Doctrine\ORM\Query\DTORSM;

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
        $values = $q->getSingleResult();
        $random = rand($values[2], $values[1]);

        $categoryIds = array_merge([0], array_map(function($product) { return $product->categoryId; }, $products));

        while ($query->count--) {
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
                INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
                INNER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.id
                INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId
                INNER JOIN AppBundle:Category AS c WITH c.id = cp.id
                WHERE
                    bp.id >= :random
                    AND bp.categoryId NOT IN (:categoryIds)
                    AND COALESCE(p.price, p2.price) > 0 AND p2.geoCityId = 0
                    AND COALESCE(p.productAvailabilityCode, p2.productAvailabilityCode) = :available
            ");
            $q->setParameter('random', $random);
            $q->setParameter('categoryIds', $categoryIds);
            $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
            $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
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

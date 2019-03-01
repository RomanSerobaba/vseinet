<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockSpecialsQueryHandler extends MessageHandler
{
    public function handle(GetBlockSpecialsQuery $query)
    {
        $products = [];

        $cache = $this->get('cache.provider.memcached');
        $cachedBlock = $cache->getItem('block_specials_'.$this->getGeoCity()->getRealId().'_'.$query->categoryId);
        if ($cachedBlock->isHit()) {
            foreach ($cachedBlock->get() as $id) {
                $cachedProduct = $cache->getItem('block_specials_product_'.$id.'_'.$query->categoryId);
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

        while ($query->count--) {
            $random = rand($values[2], $values[1]);
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
                    AND bp.id NOT IN (:ids)
                    AND COALESCE(p.price, p2.price) > 0 AND p2.geoCityId = 0
                    AND COALESCE(p.productAvailabilityCode, p2.productAvailabilityCode) = :available
                    AND cp.pid = :categoryId
            ");
            $q->setParameter('random', $random);
            $q->setParameter('ids', empty($products) ? [0] : array_keys($products));
            $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
            $q->setParameter('categoryId', $query->categoryId);
            $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
            $q->setMaxResults(1);
            try {
                $product = $q->getSingleResult();
                $products[$product->id] = $product;

                $cachedProduct = $cache->getItem('block_specials_product_'.$product->id.'_'.$query->categoryId);
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

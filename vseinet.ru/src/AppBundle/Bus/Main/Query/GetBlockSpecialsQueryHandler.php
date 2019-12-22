<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;
use Doctrine\ORM\AbstractQuery;

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
            WHERE bp.id = bp.canonicalId
        ');
        $baseProductIds = $q->getSingleResult();

        $excludeIdsSpec = '';
        if (1 < $query->count + count($products)) {
            $excludeIdsSpec = 'AND bp.id NOT IN (:ids)';
        }

        $categoryIdSpec = '';
        $categoryJoinSpec = '';
        if (0 < $query->categoryId) {
            $categoryIdSpec = 'AND cp.pid = :categoryId';
            $categoryJoinSpec = '
                INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId
                INNER JOIN AppBundle:Category AS c WITH c.id = cp.id
            ';
        }

        while ($query->count--) {
            $randomId = rand($baseProductIds[1], $baseProductIds[2]);
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
                            '',
                            p.price,
                            bpi.basename,
                            bp.sefUrl
                        )
                    FROM AppBundle:BaseProduct AS bp
                    INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND bpi.width > 0
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId AND p.productAvailabilityCode = :available AND p.price > 0
                    {$categoryJoinSpec}
                    WHERE bp.id >= :randomId {$excludeIdsSpec} {$categoryIdSpec} AND bp.id = bp.canonicalId
                ")
                    ->setParameters([
                        'randomId' => $randomId,
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
                            '',
                            p.price,
                            bpi.basename,
                            bp.sefUrl
                        )
                    FROM AppBundle:BaseProduct AS bp
                    INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND bpi.width > 0
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = 0 AND p.productAvailabilityCode = :on_demand AND p.price > 0
                    {$categoryJoinSpec}
                    WHERE bp.id >= :randomId {$excludeIdsSpec} {$categoryIdSpec} AND bp.id = bp.canonicalId
                ")
                    ->setParameters([
                        'randomId' => $randomId,
                        'on_demand' => ProductAvailabilityCode::ON_DEMAND
                    ]);
            }

            if ($excludeIdsSpec) {
                $q->setParameter('ids', empty($products) ? [0] : array_keys($products));
            }
            if ($categoryIdSpec) {
                $q->setParameter('categoryId', $query->categoryId);
            }
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

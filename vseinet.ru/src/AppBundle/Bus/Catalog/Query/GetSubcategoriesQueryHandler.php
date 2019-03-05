<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetSubcategoriesQueryHandler extends MessageHandler
{
    public function handle(GetSubcategoriesQuery $query)
    {
        $cache = $this->get('cache.provider.memcached');
        $cachedCats = $cache->getItem('subcategories_'.$query->pid);
        if ($cachedCats->isHit()) {
            return $cachedCats->get();
        }

        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Query\DTO\Subcategory (
                    c.id,
                    c.name,
                    c.aliasForId,
                    c.countProducts
                )
            FROM AppBundle:Category c
            WHERE c.pid = :pid AND c.countProducts > 0 AND c.id != 7562
            ORDER BY c.name
        ");
        $q->setParameter('pid', $query->pid);
        $subcategories = $q->getArrayResult();

        if (!$this->getUserIsEmployee()) {
            $subcategories = array_filter($subcategories, function ($subcategory) {
                return 0 < $subcategory->countProducts;
            });
        }

        foreach ($subcategories as $subcategory) {
            $q = $em->createQuery('
                SELECT
                    bpi.basename,
                    RANDOM() HIDDEN ORD
                FROM AppBundle:BaseProductImage bpi
                INNER JOIN AppBundle:BaseProduct bp WITH bp.id = bpi.baseProductId
                INNER JOIN AppBundle:CategoryPath cp WITH cp.id = bp.categoryId
                WHERE cp.pid = :categoryId
                ORDER BY ORD
            ');
            $q->setParameter('categoryId', $subcategory->id);
            $q->setMaxResults(1);
            $image = $q->getOneOrNullResult();
            if (!empty($image)) {
                $subcategory->baseSrc = $image['basename'];
            }
        }

        $cachedCats->set($subcategories);
        $cachedCats->expiresAfter(600 + rand(0, 300));
        $cache->save($cachedCats);

        return $subcategories;
    }
}

<?php

namespace AppBundle\Bus\Search\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetCounterQueryHandler extends MessageHandler
{
    public function handle(GetCounterQuery $query)
    {
        $cache = $this->get('cache.provider.memcached');
        $cachedCount = $cache->getItem('count_products');
        if ($cachedCount->isHit()) {
            return $cachedCount->get();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT c.countProducts
            FROM AppBundle:Category c
            WHERE c.id = 0
        ");
        try {
            $count = $q->getSingleScalarResult();

            $cachedCount->set($count);
            $cachedCount->expiresAfter(300 + rand(0, 100));
            $cache->save($cachedCount);

        } catch (\Exception $e) {
            $count = 0;
        }

        return $count;
    }
}

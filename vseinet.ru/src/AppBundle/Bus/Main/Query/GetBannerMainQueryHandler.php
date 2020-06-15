<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetBannerMainQueryHandler extends MessageHandler
{
    public function handle(GetBannerMainQuery $query)
    {
        $cache = $this->get('cache.provider.memcached');
        $cachedBanners = $cache->getItem('banners');
        if ($cachedBanners->isHit()) {
            return $cachedBanners->get();
        }

        $result = ['banners' => []];

        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT b
            FROM AppBundle:Banner AS b
            WHERE
                COALESCE(b.activeSince, CURRENT_TIMESTAMP()) <= CURRENT_TIMESTAMP()
                AND COALESCE(b.activeTill, CURRENT_TIMESTAMP()) >= CURRENT_TIMESTAMP()
            ORDER BY b.priority, b.id DESC
        ");
        $banners = $q->getResult();
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $result['banners'][$banner->getId()] = $banner;
            }
        }

        $cachedBanners->set($result);
        $cachedBanners->expiresAfter(300 + rand(0, 100));
        $cache->save($cachedBanners);

        return $result;
    }
}

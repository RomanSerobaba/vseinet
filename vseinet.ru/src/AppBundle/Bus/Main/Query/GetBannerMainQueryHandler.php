<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\BannerMainData;
use AppBundle\Entity\BannerMainProductData;

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
            FROM AppBundle:BannerMainData AS b
            WHERE
                b.isVisible = true
                AND (b.startVisibleDate IS NULL OR b.startVisibleDate >= CURRENT_TIMESTAMP())
                AND (b.endVisibleDate IS NULL OR b.endVisibleDate <= CURRENT_TIMESTAMP())
            ORDER BY b.weight, b.id
        ");
        $banners = $q->getResult();
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $result['banners'][$banner->getId()] = $banner;
            }

            $products = $em->getRepository(BannerMainProductData::class)->findBy(['bannerId' => array_keys($result['banners'])], ['id' => 'ASC']);
            $bannerId2products = [];
            foreach ($products as $product) {
                $bannerId2products[$product->getBannerId()][] = $product;
            }

            $result['products'] = $bannerId2products;
        }

        $cachedBanners->set($result);
        $cachedBanners->expiresAfter(300 + rand(0, 100));
        $cache->save($cachedBanners);

        return $result;
    }
}

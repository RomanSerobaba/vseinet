<?php 

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\BannerMainData;
use AppBundle\Entity\BannerMainProductData;

class GetBannerMainQueryHandler extends MessageHandler
{
    public function handle(GetBannerMainQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT b
            FROM AppBundle:BannerMainData AS b
            WHERE 
                b.isVisible = true 
                AND (b.startVisibleDate IS NULL OR b.startVisibleDate >= CURRENT_TIMESTAMP())
                AND (b.endVisibleDate IS NULL OR b.endVisibleDate <= CURRENT_TIMESTAMP()) 
            ORDER BY b.id
        ");

        $banners = [];
        foreach ($q->getResult() as $banner) {
            $banners[$banner->getId()] = $banner;
        }

        if (empty($banners)) {
            return ['banners' => []];
        }

        $products = $em->getRepository(BannerMainProductData::class)->findBy(['bannerId' => array_keys($banners)], ['id' => 'ASC']);
        $bannerId2products = [];
        foreach ($products as $product) {
            $bannerId2products[$product->getBannerId()][] = $product;
        }

        return ['banners' => $banners, 'products' => $bannerId2products];
    }
}

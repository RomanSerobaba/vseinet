<?php 

namespace ShopBundle\Bus\Banner\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetSiteListQueryHandler extends MessageHandler
{
    public function handle(GetSiteListQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW ShopBundle\Bus\Banner\Query\DTO\SiteBanners (
                    b.id,
                    b.type,
                    b.url,
                    b.title,
                    b.isVisible,
                    b.tabIsFixed,
                    b.startVisibleDate,
                    b.endVisibleDate,
                    b.backgroundColor,
                    b.titleTextColor,
                    b.templatesId,
                    b.tabBackgroundColor,
                    b.tabTextColor,
                    b.tabImg,
                    b.tabText
                )
            FROM
                ShopBundle:BannerMainData b
            WHERE 
                b.isVisible = TRUE
        ');
        /* AND NOW() BETWEEN start_visible_date AND end_visible_date*/

        /**
         * @var $banners \ShopBundle\Bus\Banner\Query\DTO\SiteBanners[]
         */
        $banners = $q->getResult();

        $bannerIds = array_flip(array_map(function ($banner) { return $banner->id; }, $banners));

        $q = $em->createQuery('
                SELECT
                    NEW ShopBundle\Bus\Banner\Query\DTO\ProductData (
                        p.id,
                        p.baseProductId,
                        p.bannerId,
                        p.title,
                        p.titlePc,
                        p.titleTablet,
                        p.titlePhone,
                        p.photoPc,
                        p.photoTablet,
                        p.photoPhone,
                        p.price,
                        p.salePrice
                    )
                FROM
                    ShopBundle:BannerMainProductData p
                WHERE
                    p.bannerId IN(:ids)
            ');
        $q->setParameter('ids', array_keys($bannerIds));

        /**
         * @var $products \ShopBundle\Bus\Banner\Query\DTO\ProductData[]
         */
        $products = $q->getResult();

        foreach ($products as $product) {
            $banners[$bannerIds[$product->bannerId]]->productList[] = $product;
        }

        return $banners;
    }
}
<?php 

namespace ShopBundle\Bus\Banner\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetBannerQueryHandler extends MessageHandler
{
    public function handle(GetBannerQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW ShopBundle\Bus\Banner\Query\DTO\Banner (
                    b.id,
                    b.type,
                    b.weight,
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
                    b.tabText,
                    b.imgBackgroundPc,
                    b.imgBackgroundTablet,
                    b.imgBackgroundPhone,
                    b.descriptionPc,
                    b.descriptionTablet,
                    b.descriptionPhone,
                    b.titlePc,
                    b.titleTablet,
                    b.titlePhone,
                    b.textUrlPc,
                    b.textUrlTablet,
                    b.textUrlPhone,
                    b.posBackgroundPcX,
                    b.posBackgroundPcY,
                    b.posBackgroundTabletX,
                    b.posBackgroundTabletY,
                    b.posBackgroundPhoneX,
                    b.posBackgroundPhoneY,
                    b.leftDetailsPc,
                    b.leftDetailsTablet,
                    b.leftDetailsPhone,
                    b.rightDetailsPc,
                    b.rightDetailsTablet,
                    b.rightDetailsPhone
                )
            FROM
                ShopBundle:BannerMainData b
            WHERE
                b.id = :id
        ');
        $q->setParameter('id', $query->id);

        $rows = $q->getResult();
        $banner = array_shift($rows);

        if ($banner) {
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
                    p.bannerId = :id
            ');
            $q->setParameter('id', $query->id);

            $banner->productList = $q->getResult();
        }

        return $banner;
    }
}
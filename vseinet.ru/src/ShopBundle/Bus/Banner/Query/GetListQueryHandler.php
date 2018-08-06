<?php 

namespace ShopBundle\Bus\Banner\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW ShopBundle\Bus\Banner\Query\DTO\Banners (
                    b.id,
                    b.type,
                    b.weight,
                    b.title,
                    b.isVisible,
                    b.tabIsFixed
                )
            FROM
                ShopBundle:BannerMainData b
            ORDER BY
                b.weight DESC
        ');

        return $q->getResult();
    }
}
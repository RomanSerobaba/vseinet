<?php 

namespace ShopBundle\Bus\BannerTemplate\Query;

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
                NEW ShopBundle\Bus\BannerTemplate\Query\DTO\BannerTemplates (
                    t.id,
                    t.name
                )
            FROM
                ShopBundle:BannerMainTemplate t
        ');

        return $q->getResult();
    }
}
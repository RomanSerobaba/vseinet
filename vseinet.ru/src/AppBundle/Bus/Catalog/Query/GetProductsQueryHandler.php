<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bpi.basename,
                    COALESCE(p.productAvailabilityCode, p2.productAvailabilityCode),
                    COALESCE(p.price, p2.price),
                    COALESCE(p.priceType, p2.priceType),
                    bpd.shortDescription,
                    bp.minQuantity,
                    bp.updatedAt
                )
            FROM AppBundle:BaseProduct bp
            INNER JOIN AppBundle:BaseProductData bpd WITH bpd.baseProductId = bp.id
            LEFT JOIN AppBundle:Product p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product p2 WITH p2.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:BaseProductImage bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 AND p2.geoCityId = 0
            WHERE bp.id IN (:ids)
        ");
        $q->setParameter('ids', $query->ids);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $products = $q->getResult('IndexByHydrator');

        $sorted = [];
        foreach ($query->ids as $id) {
            if (isset($products[$id])) {
                $sorted[] = $products[$id];
            }
        }

        return $sorted;
    }
}

<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query)
    {
        if ($this->getUserIsEmployee()) {
            $userId = $this->getUser()->getId();
        } else {
            $userId = 0;
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bpi.basename,
                    COALESCE(p.productAvailabilityCode, p0.productAvailabilityCode),
                    COALESCE(p.price, p0.price),
                    COALESCE(p.priceType, p0.priceType),
                    bpd.shortDescription,
                    bp.minQuantity,
                    bp.updatedAt,
                    CASE WHEN ppb.baseProductId IS NULL THEN false ELSE true END
                )
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:BaseProductData AS bpd WITH bpd.baseProductId = bp.id
            LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p0 WITH p0.baseProductId = bp.id AND p0.geoCityId = 0
            LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            LEFT OUTER JOIN AppBundle:ProductPricetagBuffer AS ppb WITH ppb.baseProductId = bp.id AND ppb.createdBy = :userId
            WHERE bp.id IN (:ids)
        ");
        $q->setParameter('ids', $query->ids);
        $q->setParameter('geoCityId', $this->getGeoCity()->getId());
        $q->setParameter('userId', $userId);
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

<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Enum\ProductPriceType;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->getUserIsEmployee()) {
            $userId = $this->getUser()->getId();
        } else {
            $userId = 0;
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Product\Query\DTO\BaseProduct (
                    bp.id,
                    bp.name,
                    bpd.exname,
                    bp.categoryId,
                    bp.brandId,
                    COALESCE(p.productAvailabilityCode, p0.productAvailabilityCode),
                    COALESCE(p.price, p0.price),
                    COALESCE(p.priceType, p0.priceType),
                    bp.minQuantity,
                    bpd.model,
                    bpd.manufacturerLink,
                    bpd.manualLink,
                    d.description,
                    bp.estimate,
                    bp.canonicalId,
                    ppb.quantity
                )
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:BaseProductData AS bpd WITH bpd.baseProductId = bp.id
            LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p0 WITH p0.baseProductId = bp.id AND p0.geoCityId = 0
            LEFT OUTER JOIN AppBundle:BaseProductDescription AS d WITH d.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:ProductPricetagBuffer AS ppb WITH ppb.baseProductId = bp.id AND ppb.createdBy = :userId
            WHERE bp.id = :id
        ");
        $q->setParameter('id', $query->id);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setParameter('userId', $userId);
        $baseProduct = $q->getOneOrNullResult();
        if (!$baseProduct instanceof DTO\BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->id));
        }

        $baseProduct->priceTypeName = ProductPriceType::getName($baseProduct->priceType);

        return $baseProduct;
    }
}

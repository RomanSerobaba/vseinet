<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsConditionCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Enum\ProductPriceTypeCode;

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
                    bp.canonicalId,
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
                    ppb.quantity,
                    COALESCE(FIRST(
                        SELECT ROUND(SUM(grrc.delta * (si.purchasePrice - si.bonusAmount)) / SUM(grrc.delta))
                        FROM AppBundle:GoodsReserveRegisterCurrent AS grrc
                        INNER JOIN AppBundle:SupplyItem AS si WITH si.id = grrc.supplyItemId
                        WHERE grrc.baseProductId = bp.id AND grrc.goodsConditionCode = :goodsConditionCode_FREE
                    ), bp.supplierPrice)
                )
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:BaseProductData AS bpd WITH bpd.baseProductId = bp.id
            LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.canonicalId AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p0 WITH p0.baseProductId = bp.canonicalId AND p0.geoCityId = 0
            LEFT OUTER JOIN AppBundle:BaseProductDescription AS d WITH d.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:ProductPricetagBuffer AS ppb WITH ppb.baseProductId = bp.canonicalId AND ppb.createdBy = :userId
            WHERE bp.id = :id
        ");
        $q->setParameter('id', $query->id);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setParameter('userId', $userId);
        $q->setParameter('goodsConditionCode_FREE', GoodsConditionCode::FREE);
        $baseProduct = $q->getOneOrNullResult();
        if (!$baseProduct instanceof DTO\BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->id));
        }

        $baseProduct->priceTypeName = ProductPriceTypeCode::getName($baseProduct->priceType);

        return $baseProduct;
    }
}

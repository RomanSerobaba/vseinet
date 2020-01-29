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
                    bp.id,
                    bp.name,
                    bpd.exname,
                    bp.categoryId,
                    bp.brandId,
                    COALESCE(p.productAvailabilityCode, p0.productAvailabilityCode),
                    COALESCE(p.price, p0.price),
                    COALESCE(p.priceTypeCode, p0.priceTypeCode),
                    bp.minQuantity,
                    bpd.model,
                    bpd.manufacturerLink,
                    bpd.manualLink,
                    d.description,
                    bp.estimate,
                    bp.id,
                    ppb.quantity,
                    COALESCE(FIRST(
                        SELECT ROUND(SUM(grrc.delta * (si.purchasePrice - si.bonusAmount + si.charges)) / SUM(grrc.delta))
                        FROM AppBundle:GoodsReserveRegisterCurrent AS grrc
                        JOIN AppBundle:SupplyItem AS si WITH si.id = grrc.supplyItemId
                        JOIN AppBundle:BaseProduct AS bp2 WITH bp2.id = grrc.baseProductId
                        WHERE bp2.canonicalId = bp.id AND grrc.goodsConditionCode = :goodsConditionCode_FREE
                    ), bp.supplierPrice),
                    COALESCE(p.competitorPrice, p0.competitorPrice),
                    FIRST(
                        SELECT
                            ppl.operatedAt
                        FROM
                            AppBundle:ProductPriceLog AS ppl
                        WHERE
                            ppl.baseProductId = bp.id
                        ORDER BY
                            ppl.operatedAt DESC
                    ),
                    FIRST(
                        SELECT
                            CONCAT_WS(' ', per.lastname, per.firstname, per.secondname)
                        FROM
                            AppBundle:ProductPriceLog AS ppl2
                        INNER JOIN
                            AppBundle:User AS u WITH ppl2.operatedBy = u.id
                        INNER JOIN
                            AppBundle:Person AS per WITH per.id = u.personId
                        WHERE
                            ppl2.baseProductId = bp.id
                        ORDER BY
                            ppl2.operatedAt DESC
                    ),
                    bp.sefUrl,
                    c.sefUrl
                )
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:Category AS c WITH c.id = bp.categoryId
            INNER JOIN AppBundle:BaseProduct AS bpo WITH bpo.canonicalId = bp.id
            INNER JOIN AppBundle:BaseProductData AS bpd WITH bpd.baseProductId = bp.id
            LEFT JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p0 WITH p0.baseProductId = bp.id AND p0.geoCityId = 0
            LEFT OUTER JOIN AppBundle:BaseProductDescription AS d WITH d.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:ProductPricetagBuffer AS ppb WITH ppb.baseProductId = bp.id AND ppb.createdBy = :userId
            WHERE bpo.id = :id
        ");
        $q->setParameter('id', $query->id);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $q->setParameter('userId', $userId);
        $q->setParameter('goodsConditionCode_FREE', GoodsConditionCode::FREE);
        $baseProduct = $q->getOneOrNullResult();
        if (!$baseProduct instanceof DTO\BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->id));
        }

        $baseProduct->priceTypeName = ProductPriceTypeCode::getName($baseProduct->priceTypeCode);

        return $baseProduct;
    }
}

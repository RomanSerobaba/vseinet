<?php

namespace AdminBundle\Bus\Supplier\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\BaseProductHistoryObject;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\RepresentativeTypeCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetRemainsQueryHandler extends MessageHandler
{
    public function handle(GetRemainsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery('
            SELECT
                NULL AS id,
                NULL AS supplier_code,
                NULL AS article,
                NULL AS code,
                bp.name,
                :productAvailabilityCode_AVAILABLE::product_availability_code AS product_availability_code,
                ROUND(SUM(grrc.delta * (si.purchase_price - si.bonus_amount - si.extra_discount_amount + si.charges)) / SUM(grrc.delta)) AS price,
                NULL AS price_time,
                NULL AS transfered_by,
                NULL AS transfered_at
            FROM base_product AS bp
            INNER JOIN base_product AS bp2 ON bp.id = bp2.canonical_id
            INNER JOIN goods_reserve_register_current AS grrc ON grrc.base_product_id = bp2.id
            INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id
            LEFT OUTER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
            LEFT OUTER JOIN representative AS r ON r.geo_point_id = gr.geo_point_id
            WHERE bp.id = :base_product_id AND grrc.goods_condition_code = :goodsConditionCode_FREE AND r.type != :representativeTypeCode_FRANCHISER
            GROUP BY bp.name
        ', new DTORSM(DTO\Remain::class));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('productAvailabilityCode_AVAILABLE', ProductAvailabilityCode::AVAILABLE);
        $q->setParameter('goodsConditionCode_FREE', GoodsConditionCode::FREE);
        $q->setParameter('representativeTypeCode_FRANCHISER', RepresentativeTypeCode::FRANCHISER);
        $remains = $q->getResult('DTOHydrator');

        $q = $em->createNativeQuery("
            WITH transfer_info AS (
                SELECT
                    bph.added_at as transfered_at,
                    CONCAT(p.lastname, ' ', p.firstname, ' ', p.secondname) AS transfered_by,
                    CAST(bph.new_value AS int) as partner_product_id
                FROM
                    base_product_history AS bph
                    LEFT OUTER JOIN org_employee AS oe ON oe.user_id = bph.user_id
                    LEFT OUTER JOIN \"user\" AS u ON u.id = oe.user_id
                    LEFT OUTER JOIN person AS p ON p.id = u.person_id
                    LEFT OUTER JOIN base_product_history AS bph2 ON bph2.base_product_id = bph.base_product_id AND bph2.object = :baseProductHistoryObject_PARTNER_PRODUCT AND (bph.added_at < bph2.added_at OR bph.added_at = bph2.added_at AND bph.id < bph2.id)
                WHERE
                    bph.base_product_id = :base_product_id AND bph2.id IS NULL AND bph.object = :baseProductHistoryObject_PARTNER_PRODUCT
            )
            SELECT
                pp.id,
                s.code AS supplier_code,
                pp.article,
                pp.code,
                pp.name,
                sp.product_availability_code,
                sp.price,
                ROUND(sp.initial_price * (1.0 + (s.delivery_cost_percent - s.price_discount_percent) / 100)) AS margin_base_price,
                sp.initial_price,
                sp.updated_at AS price_time,
                ti.transfered_by,
                ti.transfered_at
            FROM partner_product AS pp
            INNER JOIN supplier_product AS sp ON sp.partner_product_id = pp.id
            INNER JOIN supplier AS s ON s.id = sp.supplier_id
            INNER JOIN base_product AS bp ON bp.id = sp.base_product_id
            LEFT OUTER JOIN transfer_info AS ti ON ti.partner_product_id = pp.id
            WHERE bp.canonical_id = :base_product_id
            ORDER BY sp.product_availability_code DESC, sp.price, sp.updated_at DESC
        ", new DTORSM(DTO\Remain::class));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('baseProductHistoryObject_PARTNER_PRODUCT', BaseProductHistoryObject::PARTNER_PRODUCT);
        $remains = array_merge($remains, $q->getResult('DTOHydrator'));

        return $remains;
    }
}

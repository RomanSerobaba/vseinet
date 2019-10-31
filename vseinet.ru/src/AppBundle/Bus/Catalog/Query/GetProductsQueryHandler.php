<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\ProductPriceTypeCode;

class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query)
    {
        if ($this->getUserIsEmployee()) {
            $userId = $this->getUser()->getId();
        } else {
            $userId = 0;
        }

        $q = $this->getDoctrine()->getManager()->createNativeQuery("
            SELECT
                b.id,
                b.name,
                bpi.basename AS base_src,
                COALESCE ( p2.product_availability_code, p.product_availability_code ) AS availability,
                COALESCE ( p2.price, p.price ) AS price,
                COALESCE ( p2.price_type, p.price_type ) AS price_type,
                bpd.short_description AS description,
                b.min_quantity,
                b.updated_at,
                ppb.quantity AS pricetag_quantity,
                COALESCE((
                    SELECT ROUND(SUM(grrc.delta * (si.purchase_price - si.bonus_amount)) / SUM(grrc.delta))
                    FROM goods_reserve_register_current AS grrc
                    INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id
                    WHERE grrc.base_product_id = b.id AND grrc.goods_condition_code = :goodsConditionCode_FREE
                ), b.supplier_price) AS purchase_price
            FROM
                base_product AS b
                INNER JOIN base_product_data AS bpd ON ( bpd.base_product_id = b.ID )
                LEFT JOIN product AS p2 ON ( p2.base_product_id = b.ID AND p2.geo_city_id = :geoCityId )
                INNER JOIN product AS p ON ( p.base_product_id = b.ID AND p.geo_city_id = 0 )
                LEFT JOIN base_product_image AS bpi ON ( bpi.base_product_id = b.ID AND bpi.sort_order = 1 )
                LEFT JOIN product_pricetag_buffer AS ppb ON ( ppb.base_product_id = b.ID AND ppb.created_by = :userId )
                JOIN UNNEST ( :ids::INT [] ) WITH ORDINALITY T ( ID, ord ) ON b.ID = T.ID
            WHERE
                b.id = b.canonical_id
            ORDER BY
                T.ord
        ", new DTORSM(DTO\Product::class))
            ->setParameters([
                'geoCityId' => $this->getGeoCity()->getId(),
                'userId' => $userId,
                'ids' => '{'.implode(',', $query->ids).'}',
                'goodsConditionCode_FREE' => GoodsConditionCode::FREE,
            ]);
        $products = [];

        foreach ($q->getResult('DTOHydrator') as $product) {
            $product->priceTypeName = ProductPriceTypeCode::getName($product->priceType);
            $products[$product->id] = $product;
        }

        $baseProductsIds = array_keys(array_filter($products, function($product) {
            return in_array($product->availability, [ProductAvailabilityCode::ON_DEMAND, ProductAvailabilityCode::IN_TRANSIT]);
        }));

        if ($baseProductsIds) {
            $deliveryDates = $this->get('query_bus')->handle(new \AppBundle\Bus\Product\Query\GetDeliveryDateQuery(['baseProductIds' => $baseProductsIds]));

            if (!empty($deliveryDates)) {
                foreach ($deliveryDates as $baseProductId => $deliveryDate) {
                    $products[$baseProductId]->deliveryDate = $deliveryDate->date;
                }
            }
        }

        $sorted = [];
        foreach ($query->ids as $id) {
            if (isset($products[$id])) {
                $sorted[] = $products[$id];
            }
        }

        return $sorted;
    }
}

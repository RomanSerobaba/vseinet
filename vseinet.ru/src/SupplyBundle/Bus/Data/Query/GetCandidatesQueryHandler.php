<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Specification\ViewSupplierProductSpecification;
use ContentBundle\Entity\BaseProductImage;
use ContentBundle\Repository\BaseProductImageRepository;
use AppBundle\ORM\Query\DTORSM;

class GetCandidatesQueryHandler extends MessageHandler
{
    public function handle(GetCandidatesQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $spec = new ViewSupplierProductSpecification();

        // Список товаров
        $q = $em->createNativeQuery('
            SELECT
                bp.id /*ид товара*/,
                round( SUM( srr.quantity * srr.purchase_price ) / SUM( srr.quantity ) ) :: INTEGER AS purchase_price /*цена закупки*/,
                round( SUM( srr.quantity * srr.purchase_price ) / SUM( srr.quantity ) ) :: INTEGER AS bonus_purchase_price /*цена с бонусом*/,
                round(((sp.price :: FLOAT / SUM( COALESCE ( nullif( srr.purchase_price, 0 ), sp.price ) * srr.quantity ) :: FLOAT * SUM( srr.quantity )) * 100 - 100) :: NUMERIC, 1) :: FLOAT AS pricelist_discount /*скидка от прайса*/,
                sp.code /*код товара у поставщика*/,
                COALESCE ( sp.name, bp.name ) as name /*наименование товара у поставщика*/,
                bpi.basename AS photo_url /*урл картинки*/,
                SUM( srr.quantity )::INTEGER AS quantity /*требуемое к отгрузке количество*/ 
            FROM (
                SELECT
                    SUM( gnr.delta ) AS quantity,
                    gnr.base_product_id,
                    bp.supplier_price AS purchase_price,
                    s.supplier_id 
                FROM
                    get_goods_need_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP ) AS gnr
                    JOIN base_product AS bp ON bp.id = gnr.base_product_id
                    JOIN supply AS s ON s.supplier_id = bp.supplier_id
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    s.id = :supply_id 
                    AND s.destination_point_id IN ( o.geo_point_id, :point_id ) 
                GROUP BY
                    gnr.base_product_id,
                    bp.supplier_price,
                    s.supplier_id 
                HAVING
                    SUM( gnr.delta ) > 0 
                
                UNION ALL
                
                SELECT
                    SUM( srr.delta ) AS quantity,
                    srr.base_product_id,
                    srr.purchase_price,
                    srr.supplier_id 
                FROM
                    supply AS s
                    JOIN supplier_reserve AS sr ON s.supplier_id = sr.supplier_id 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = sr.supplier_id 
                        AND ssr.is_shipping = TRUE 
                        AND ssr.closed_at IS 
                    NULL JOIN supplier_reserve_register AS srr ON srr.supplier_reserve_id = COALESCE ( ssr.id, sr.id )
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    s.id = :supply_id 
                    AND srr.supply_id IS NULL 
                    AND s.destination_point_id IN ( o.geo_point_id, :point_id ) 
                GROUP BY
                    srr.base_product_id,
                    srr.purchase_price,
                    srr.supplier_id 
                HAVING
                    SUM( srr.delta ) > 0 
                ) AS srr
                JOIN base_product AS bp ON srr.base_product_id = bp.id
                LEFT JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id 
                    AND bpi.sort_order = 1
                '.$spec->buildLeftJoin('bp.id', 'srr.supplier_id').'
            WHERE '.$spec->buildWhere(false).'
            GROUP BY
                bp.id,
                sp.code,
                sp.name,
                bpi.basename,
                sp.price 
            ORDER BY
                bp.name
        ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingProducts::class));

        $q->setParameter('supply_id', $query->id);
        $q->setParameter('point_id', $this->getParameter('default.point.id'));

        $products = $q->getResult('DTOHydrator');

        foreach ($products as &$product) {
            $product->photoUrl = $em->getRepository(BaseProductImage::class)->buildSrc($this->getParameter('product.images.web.path'), $product->photoUrl, BaseProductImageRepository::SIZE_XS);
        }

        // Список заказов
        $q = $em->createNativeQuery('
            SELECT
                bp.id AS base_product_id /*ид товара*/,
                gnr.is_reserved /*зарезервирован ли товар*/,
                oi.id::VARCHAR /*ид позиции*/,
                oi.id AS order_item_id /*ид позиции*/,
                CASE WHEN oi.id > 0 THEN TRUE ELSE FALSE END AS has_order /*наличие заказа у позиции*/,
                oi.order_id /*ид заказа*/,
                gnr.supplier_reserve_id /*ид резерва поставщика*/,
                COALESCE ( nullif( gnr.purchase_price, 0 ), sp.price ) AS purchase_price /*цена закупки*/,
                coi.retail_price /*цена продажи*/,
                COALESCE ( nullif( gnr.purchase_price, 0 ), sp.price )  AS bonus_purchase_price /*цена с бонусом*/,
                round(( sp.price :: FLOAT / COALESCE ( nullif( gnr.purchase_price :: FLOAT, 0 ), sp.price :: FLOAT ) ) :: NUMERIC * 100 - 100, 1) :: FLOAT AS pricelist_discount /*скидка от прайса*/,                
                SUM ( gnr.quantity ) :: INTEGER AS quantity /*требуемое количество*/,
                vup.fullname AS client /*имя клиента*/,
                CASE WHEN co.user_id > 0 THEN
                CONCAT_WS (
                \', \',
                NULLIF ( vup.mobile, \'\' ),
                NULLIF ( vup.phone, \'\' )) ELSE NULL 
                END AS phones /*контактные телефоны*/,
                gc.name AS city /*город*/,
                CASE WHEN EXISTS (
                    SELECT
                        1 
                    FROM
                        order_comment 
                    WHERE
                        order_id = oi.order_id 
                        AND oi.id = COALESCE ( order_item_id, oi.id )) THEN
                    TRUE ELSE FALSE 
                END AS has_comments /*есть комментарии*/
            FROM
                order_item AS oi
                LEFT JOIN client_order_item AS coi ON coi.order_item_id = oi.id 
                JOIN "order" AS o ON o.id = oi.order_id
                JOIN base_product AS bp ON bp.id = oi.base_product_id
                JOIN (
                SELECT
                    gnr.delta AS quantity,
                    gnr.base_product_id,
                    gnr.order_item_id,
                    FALSE AS is_reserved,
                    bp.supplier_price AS purchase_price,
                    NULL AS supplier_reserve_id,
                    s.supplier_id 
                FROM
                    get_goods_need_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP ) AS gnr
                    JOIN base_product AS bp ON bp.id = gnr.base_product_id
                    JOIN supply AS s ON s.supplier_id = bp.supplier_id
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    s.id = :supply_id 
                    AND s.destination_point_id IN ( o.geo_point_id, :point_id ) 
                
                UNION ALL
                
                SELECT 
                    SUM( srr.delta ) AS quantity,
                    srr.base_product_id,
                    srr.order_item_id,
                    TRUE AS is_reserved,
                    srr.purchase_price,
                    srr.supplier_reserve_id,
                    s.supplier_id 
                FROM
                    supply AS s
                    JOIN supplier_reserve AS sr ON s.supplier_id = sr.supplier_id 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    LEFT JOIN supplier_reserve AS ssr ON sr.supplier_id = ssr.supplier_id 
                        AND ssr.is_shipping = TRUE 
                        AND ssr.closed_at IS NULL 
                    JOIN supplier_reserve_register AS srr ON srr.supplier_reserve_id = COALESCE ( ssr.id, sr.id )
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    s.id = :supply_id 
                    AND srr.supply_id IS NULL 
                    AND s.destination_point_id IN ( o.geo_point_id, :point_id ) 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    srr.purchase_price,
                    srr.supplier_reserve_id,
                    s.supplier_id 
                HAVING
                    SUM ( srr.delta ) > 0 
                ) AS gnr ON oi.id = gnr.order_item_id
                LEFT JOIN client_order AS co ON co.order_id = o.id 
                JOIN func_view_user_person (COALESCE ( co.user_id, o.manager_id )) AS vup ON vup.user_ID = COALESCE ( co.user_id, o.manager_id )
                JOIN geo_city AS gc ON gc.id = o.geo_city_id
                '.$spec->buildLeftJoin('bp.id', 'gnr.supplier_id').'
            WHERE '.$spec->buildWhere(false).'
            GROUP BY
                bp.id,
                oi.id,
                vup.fullname,
                vup.mobile,
                vup.phone,
                gc.name,
                o.created_at,
                coi.retail_price,
                co.user_id,
                gnr.is_reserved,
                gnr.purchase_price,
                gnr.supplier_reserve_id,
                sp.price 
            ORDER BY
                o.created_at
        ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingOrders::class));

        $q->setParameter('supply_id', $query->id);
        $q->setParameter('point_id', $this->getParameter('default.point.id'));

        $orders = $q->getResult('DTOHydrator');

        return ['products' => $products, 'orderItems' => $orders,];
    }
}
<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Specification\ViewSupplierProductSpecification;
use ContentBundle\Entity\BaseProductImage;
use ContentBundle\Repository\BaseProductImageRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class GetSupplyItemsForShippingQueryHandler extends MessageHandler
{
    public function handle(GetSupplyItemsForShippingQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                CASE WHEN s.registeredAt IS NULL 
                    THEN FALSE 
                    ELSE TRUE 
                END AS is_closed 
            FROM
                SupplyBundle:Supply s
            WHERE
                s.id = :supply_id
        ");

        $q->setParameter('supply_id', $query->id);

        $rows = $q->getArrayResult();
        $row = array_shift($rows);
        $isClosed = (bool) $row['is_closed'];

        $spec = new ViewSupplierProductSpecification();

        if (!$isClosed) {
            // Список товаров
            $q = $em->createNativeQuery('
                SELECT
                    bp.id /*ид товара*/,
                    ROUND( SUM( srr.purchase_price * srr.delta ) / SUM( srr.delta ) )::INTEGER AS purchase_price /*цена закупки*/,
                    ROUND( SUM( srr.purchase_price * srr.delta ) / SUM( srr.delta ) )::INTEGER AS bonus_purchase_price /*цена с бонусом*/,
                    round(( sp.price::FLOAT / SUM( srr.purchase_price * srr.delta )::FLOAT * SUM( srr.delta )::FLOAT )::NUMERIC * 100 - 100, 1)::FLOAT AS pricelist_discount /*скидка от прайса*/,
                    sp.code /*код товара у поставщика*/,
                    COALESCE ( sp.name, bp.name ) as name /*наименование товара у поставщика*/,
                    bpi.basename AS photo_url /*урл картинки*/,
                    SUM( srr.delta )::INTEGER AS quantity /*требуемое к отгрузке количество*/
                FROM
                    supplier_reserve_register AS srr
                    JOIN base_product AS bp ON srr.base_product_id = bp.id
                    LEFT JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id 
                        AND bpi.sort_order = 1
                    '.$spec->buildLeftJoin('bp.id', 'srr.supplier_id').'
                WHERE
                    srr.supply_id = :supply_id '.$spec->buildWhere().'
                GROUP BY
                    bp.id,
                    sp.code,
                    sp.name,
                    bpi.basename,
                    sp.price
                HAVING
                    SUM( srr.delta ) > 0 
                ORDER BY
                    bp.name
            ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingProducts::class));

            $q->setParameter('supply_id', $query->id);

            $products = $q->getResult('DTOHydrator');

            foreach ($products as &$product) {
                $product->photoUrl = $em->getRepository(BaseProductImage::class)->buildSrc($this->getParameter('product.images.web.path'), $product->photoUrl, BaseProductImageRepository::SIZE_XS);
            }

            // Список заказов
            $q = $em->createNativeQuery('
                SELECT
                    concat ( bp.id, \'_\', oi.id, \'_\', srr.purchase_price ) AS id,
                    bp.id AS base_product_id /*ид товара*/,
                    oi.id AS order_item_id /*ид позиции*/,
                    oi.order_id /*ид заказа*/,
                    CASE when oi.id > 0 THEN TRUE ELSE FALSE END AS has_order /*наличие заказа у позиции*/  ,
                    srr.supplier_reserve_id /* ид резерва поставщика */,
                    srr.purchase_price /*цена закупки*/,
                    coi.retail_price /*цена продажи*/,
                    srr.purchase_price AS bonus_purchase_price /*цена с бонусом*/,
                    round( ( ( sp.price :: FLOAT / srr.purchase_price :: FLOAT ) * 100 - 100 ) :: NUMERIC, 1 ) AS pricelist_discount /*скидка от прайса*/,
                    SUM( srr.delta ) AS quantity /*количество*/,
                    vup.fullname AS client /*имя клиента*/,
                    CASE WHEN co.user_id > 0 
                        THEN CONCAT_WS ( \', \', NULLIF ( vup.mobile, \'\' ), NULLIF ( vup.phone, \'\' ) ) 
                        ELSE NULL 
                    END AS phones /*контактные телефоны*/,
                    gc.name AS city /*город*/,
                    CASE WHEN EXISTS ( SELECT 1 FROM order_comment WHERE order_id = oi.order_id AND oi.id = COALESCE ( order_item_id, oi.id ) ) 
                        THEN TRUE 
                        ELSE FALSE 
                    END AS has_comments /*есть комментарии*/
                FROM
                    supplier_reserve_register AS srr
                    LEFT JOIN order_item AS oi ON srr.order_item_id = oi.id
                    LEFT JOIN client_order_item AS coi ON coi.order_item_id = oi.id
                    LEFT JOIN "order" AS o ON o.id = oi.order_id
                    JOIN base_product AS bp ON bp.id = srr.base_product_id
                    LEFT JOIN client_order AS co ON co.order_id = o.id
                    LEFT JOIN func_view_user_person ( COALESCE ( co.user_id, o.manager_id ) ) AS vup ON vup.user_id = COALESCE ( co.user_id, o.manager_id )
                    LEFT JOIN geo_city AS gc ON gc.id = o.geo_city_id
                    '.$spec->buildLeftJoin('bp.id', 'srr.supplier_id').'
                WHERE
                    srr.supply_id = :supply_id '.$spec->buildWhere().'
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
                    srr.purchase_price,
                    srr.supplier_reserve_id,
                    sp.price 
                HAVING
                    SUM( srr.delta ) > 0 
                ORDER BY
                    o.created_at
            ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingOrders::class));

            $q->setParameter('supply_id', $query->id);

            $orders = $q->getResult('DTOHydrator');
        } else {
            // Список товаров
            $q = $em->createNativeQuery('
                SELECT
                    bp.id /*ид товара*/,
                    si.purchase_price AS purchase_price /*цена закупки*/,
                    si.purchase_price - si.bonus_amount AS bonus_purchase_price /*цена с бонусом*/,
                    round( ( sp.price::FLOAT / si.purchase_price::FLOAT )::NUMERIC * 100 - 100, 1 )::FLOAT AS pricelist_discount /*скидка от прайса*/,
                    sp.code /*код товара у поставщика*/,
                    COALESCE ( sp.name, bp.name ) as name /*наименование товара у поставщика*/,
                    bpi.basename AS photo_url /*урл картинки*/,
                    si.quantity /*требуемое к отгрузке количество*/ 
                FROM
                    supply_item AS si
                    JOIN base_product AS bp ON si.base_product_id = bp.id
                    LEFT JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id 
                        AND bpi.sort_order = 1
                    JOIN supply AS s ON s.id = si.parent_doc_id
                    '.$spec->buildLeftJoin('bp.id', 's.supplier_id').'
                WHERE
                    si.parent_doc_id = :supply_id 
                    AND si.parent_doc_type = :supply
                    '.$spec->buildWhere().'
            ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingProducts::class));

            $q->setParameter('supply_id', $query->id);
            $q->setParameter('supply', DocumentTypeCode::SUPPLY);

            $products = $q->getResult('DTOHydrator');

            foreach ($products as &$product) {
                $product->photoUrl = $em->getRepository(BaseProductImage::class)->buildSrc($this->getParameter('product.images.web.path'), $product->photoUrl, BaseProductImageRepository::SIZE_XS);
            }

            // Список заказов
            $q = $em->createNativeQuery('
                SELECT
                    concat ( oi.id, \'_\', si.id ) AS id,
                    bp.id AS base_product_id /*ид товара*/,
                    oi.id AS order_item_id /*ид позиции*/,
                    oi.order_id /*ид заказа*/,
                    CASE when oi.id > 0 THEN TRUE ELSE FALSE END AS has_order /*наличие заказа у позиции*/  ,
                    coi.retail_price /*цена продажи*/,
                    grr.delta AS quantity /*количество*/,
                    vup.fullname AS client /*имя клиента*/,
                    CASE WHEN co.user_id > 0 THEN
                    CONCAT_WS (
                    \', \',
                    NULLIF ( vup.mobile,  \'\' ),
                    NULLIF (  vup.phone, \'\' ) 
                    ) ELSE NULL 
                    END AS phones /*контактные телефоны*/,
                    gc.name as city /*город*/,
                    CASE
                        WHEN EXISTS ( SELECT 1 FROM order_comment WHERE order_id = oi.order_id AND oi.id = COALESCE ( order_item_id, oi.id ) ) THEN
                    TRUE ELSE FALSE 
                    END AS has_comments /*есть комментарии*/
                FROM
                    get_goods_reserve_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP, 0, 0, NULL, NULL, 0, NULL ) AS grr
                    JOIN order_item AS oi ON grr.order_item_id = oi.id
                    LEFT JOIN client_order_item AS coi ON coi.order_item_id = oi.id
                    JOIN "order" AS o ON o.id = oi.order_id
                    JOIN base_product AS bp ON bp.id = oi.base_product_id
                    LEFT JOIN client_order AS co ON co.order_id = o.id
                    JOIN func_view_user_person ( COALESCE ( co.user_id, o.manager_id ) ) AS vup ON vup.user_ID = COALESCE ( co.user_id, o.manager_id )
                    JOIN geo_city AS gc ON gc.id = o.geo_city_id
                    JOIN supply_item AS si ON si.id = grr.supply_item_id 
                WHERE
                    si.parent_doc_type = :supply 
                    AND si.parent_doc_id = :supply_id 
                ORDER BY
                    o.created_at
            ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingOrders::class));

            $q->setParameter('supply_id', $query->id);
            $q->setParameter('supply', DocumentTypeCode::SUPPLY);

            $orders = $q->getResult('DTOHydrator');
        }

        return ['products' => $products, 'orderItems' => $orders,];
    }
}
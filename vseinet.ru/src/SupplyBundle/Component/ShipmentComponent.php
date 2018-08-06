<?php

namespace SupplyBundle\Component;

use AppBundle\Entity\User;
use ContentBundle\Entity\BaseProductImage;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsReserveType;
use ContentBundle\Repository\BaseProductImageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use OrderBundle\Entity\OrderItem;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierReserveItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShipmentComponent
{
    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em) : void
    {
        $this->_em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    public function getSupplierInvoiceOptions(int $supplierID) : array
    {
        $q = $this->getEm()->createNativeQuery('
            SELECT
                os.counteragent_id,
                os.short_name 
            FROM (
                SELECT
                    gnr.order_item_id,
                    gnr.goods_request_id 
                FROM
                    supplier_reserve AS sr
                    LEFT JOIN supplier_reserve AS srs ON srs.supplier_id = sr.supplier_id 
                        AND sr.is_shipping = TRUE 
                        AND sr.closed_at IS NULL 
                    JOIN goods_need_register AS gnr ON COALESCE ( srs.id, sr.id ) = gnr.supplier_reserve_id
                WHERE
                    sr.supplier_id = :supplier_id 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL 
                GROUP BY
                    gnr.order_item_id,
                    gnr.goods_request_id 
                HAVING
                    SUM( gnr.delta ) < 0 
            ) AS gnr
                LEFT JOIN order_item AS oi ON oi.id = gnr.order_item_id
                LEFT JOIN "order" AS o ON o.id = oi.order_id
                LEFT JOIN goods_request AS gr ON gr.id = gnr.goods_request_id
                JOIN our_seller AS os ON os.counteragent_id = COALESCE ( o.our_seller_counteragent_id, gr.our_counteragent_id ) 
            GROUP BY
                os.counteragent_id 
            ORDER BY
                os.short_name
        ', new ResultSetMapping());

        $q->setParameter('supplier_id', $supplierID);

        $ourCounteragents = $q->getResult('ListAssocHydrator');

        $q = $this->getEm()->createNativeQuery('
            SELECT
                C.id,
                C.name 
            FROM
                counteragent AS C
                JOIN supplier_to_counteragent AS stc ON stc.counteragent_id = C.id 
            WHERE
                stc.supplier_id = :supplier_id 
                AND stc.is_active = TRUE 
            ORDER BY
                stc.is_main DESC,
                C.name
        ', new ResultSetMapping());

        $q->setParameter('supplier_id', $supplierID);

        $supplierCounteragents = $q->getResult('ListAssocHydrator');

        $q = $this->getEm()->createNativeQuery('
            SELECT
                gp.id,
                concat ( gp.geo_city, \' / \', gp.code ) AS name 
            FROM
                representative AS r
                JOIN view_geo_point AS gp ON gp.id = r.geo_point_id 
            WHERE
                r.has_warehouse = TRUE
                AND r.is_active = TRUE
            ORDER BY
                gp.geo_city,
                gp.code
        ', new ResultSetMapping());

        $q->setParameter('supplier_id', $supplierID);

        $points = $q->getResult('ListAssocHydrator');

        return ['ourCounteragents' => $ourCounteragents, 'supplierCounteragents' => $supplierCounteragents, 'points' => $points,];
    }

    /**
     * Список заказов
     *
     * @param int $supplierInvoiceId
     *
     * @return array
     */
    public function getSupplierInvoiceItemsOrders(int $supplierInvoiceId) : array
    {
        // Список заказов
        $q = $this->getEm()->createNativeQuery("
            SELECT
                bp.id AS base_product_id /*ид товара*/,
                oi.id /*ид позиции*/,
                oi.order_id /*ид заказа*/,
                gnr.purchase_price /*цена закупки*/,
                coi.retail_price /*цена продажи*/,
                SUM( - gnr.delta ) AS quantity /*количество*/,
                vup.fullname AS client /*имя клиента*/,
                CASE WHEN co.user_id > 0 
                    THEN CONCAT_WS (', ', NULLIF ( vup.mobile, '' ), NULLIF ( vup.phone, '' )) 
                    ELSE NULL 
                END AS phones /*контактные телефоны*/,
                gc.name as city /*город*/,
                CASE WHEN EXISTS ( SELECT 1 FROM order_comment WHERE order_id = oi.order_id AND oi.id = COALESCE ( order_item_id, oi.id ) ) 
                    THEN TRUE 
                    ELSE FALSE 
                END AS has_comments /*есть комментарии*/
            FROM
                goods_need_register AS gnr
                LEFT JOIN order_item AS oi ON gnr.order_item_id = oi.id
                LEFT JOIN client_order_item AS coi ON coi.order_item_id = oi.id
                JOIN \"order\" AS o ON o.id = oi.order_id
                JOIN base_product AS bp ON bp.id = oi.base_product_id
                LEFT JOIN client_order AS co ON co.order_id = o.id
                JOIN func_view_user_person(COALESCE ( co.user_id, o.manager_id )) AS vup ON vup.user_ID = COALESCE ( co.user_id, o.manager_id )
                JOIN geo_city AS gc ON gc.id = o.geo_city_id 
            WHERE
                gnr.supplier_invoice_id = :invoice_id 
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
                gnr.purchase_price 
            HAVING
                SUM( - gnr.delta ) > 0 
            ORDER BY
                o.created_at
        ", new ResultSetMapping());

        $q->setParameter('supplier_invoice_id', $supplierInvoiceId);

        return $q->getResult('ListAssocHydrator');
    }

    /**
     * @param int $supplierInvoiceId
     *
     * @return array
     */
    public function getSupplierInvoiceListOrders(int $supplierInvoiceId) : array
    {
        // Список заказов
        $q = $this->getEm()->createNativeQuery("
            SELECT
                bp.id AS base_product_id /*ид товара*/,
                gnr.supplier_reserve_id AS reserve_id /*ид резерва поставщика*/,
                oi.id /*ид позиции*/,
                oi.order_id /*ид заказа*/,
                coi.retail_price /*цена продажи*/,
                gnr.purchase_price /*цена закупки*/,
                SUM ( gnr.quantity ) AS quantity /*требуемое количество*/,
                vup.fullname AS client /*имя клиента*/,
                CASE WHEN co.user_id > 0 
                    THEN CONCAT_WS (', ', NULLIF ( vup.mobile, '' ),
                        NULLIF ( vup.phone, '' )) 
                    ELSE NULL 
                END AS phones /*контактные телефоны*/,
                gc.name as city /*город*/,
                CASE WHEN EXISTS (
                    SELECT
                        1 
                    FROM
                        order_comment 
                    WHERE
                        order_id = oi.order_id 
                        AND oi.id = COALESCE ( order_item_id, oi.id )) 
                    THEN TRUE 
                    ELSE FALSE 
                END AS has_comments /*есть комментарии*/
            FROM
                order_item AS oi
                LEFT JOIN client_order_item AS coi ON coi.order_item_id = oi.id 
                JOIN \"order\" AS o ON o.id = oi.order_id
                JOIN base_product AS bp ON bp.id = oi.base_product_id
                JOIN (
                    SELECT 
                        SUM( gnr.delta ) AS quantity,
                        gnr.base_product_id,
                        gnr.order_item_id,
                        NULL AS supplier_reserve_id,
                        gnr.purchase_price
                    FROM
                        goods_need_register AS gnr
                        JOIN base_product AS bp ON bp.id = gnr.base_product_id
                        JOIN supplier_invoice AS si ON si.supplier_id = bp.supplier_id
                        JOIN order_item AS oi ON oi.id = gnr.order_item_id
                        JOIN \"order\" AS o ON o.id = oi.order_id 
                    WHERE
                        si.id = :supplier_invoice_id 
                        AND si.destination_point_id IN ( o.geo_point_id, 141 ) 
                    GROUP BY
                        gnr.base_product_id,
                        gnr.order_item_id,
                        gnr.purchase_price
                    HAVING
                        SUM ( gnr.delta ) > 0 
                        
                    UNION ALL
                    
                    SELECT 
                        SUM( - gnr.delta ) AS quantity,
                        gnr.base_product_id,
                        gnr.order_item_id,
                        gnr.supplier_reserve_id,
                        gnr.purchase_price
                    FROM
                        goods_need_register AS gnr
                        JOIN supplier_reserve AS sr ON sr.id = gnr.supplier_reserve_id
                        JOIN supplier_invoice AS si ON si.supplier_id = sr.supplier_id
                        JOIN order_item AS oi ON oi.id = gnr.order_item_id
                        JOIN \"order\" AS o ON o.id = oi.order_id 
                    WHERE
                        si.id = :supplier_invoice_id 
                        AND gnr.supplier_invoice_id IS NULL 
                        AND si.destination_point_id IN ( o.geo_point_id, 141 ) 
                    GROUP BY
                        gnr.base_product_id,
                        gnr.order_item_id,
                        gnr.supplier_reserve_id,
                        gnr.purchase_price 
                    HAVING
                        SUM ( gnr.delta ) < 0 
                ) AS gnr ON oi.id = gnr.order_item_id
                LEFT JOIN client_order AS co ON co.order_id = o.id
                JOIN func_view_user_person(COALESCE ( co.user_id, o.manager_id )) AS vup ON vup.user_ID = COALESCE ( co.user_id, o.manager_id )
                JOIN geo_city AS gc ON gc.id = o.geo_city_id 
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
                gnr.supplier_reserve_id,
                gnr.purchase_price
            ORDER BY
                o.created_at
        ", new ResultSetMapping());

        $q->setParameter('supplier_invoice_id', $supplierInvoiceId);

        return $q->getResult('ListAssocHydrator');
    }
}
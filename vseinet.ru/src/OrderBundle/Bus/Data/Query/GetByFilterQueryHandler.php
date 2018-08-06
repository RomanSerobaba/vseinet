<?php 

namespace OrderBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsReleaseType;
use AppBundle\Enum\GoodsReserveType;
use AppBundle\Enum\NotificationLogStatus;
use AppBundle\Enum\OrderItemStatusCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\Specification\ViewSupplierProductSpecification;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\ORM\Query\DTORSM;

class GetByFilterQueryHandler extends MessageHandler
{
    const SPECIAL_1 = 'special_1';
    const SPECIAL_2 = 'special_2';
    const SPECIAL_3 = 'special_3';

    const RETAIL = 'shop';
    const ONLINE = 'site';

    public function handle(GetByFilterQuery $query)
    {
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
        $em = $this->getDoctrine()->getManager();
        
        $createdSql = '';
        if (!empty($query->createdAtSince) && !empty($query->createdAtTill)) {
            $createdSql = ' AND o.created_at BETWEEN :createdAtSince AND :createdAtTill';
        } elseif (!empty($query->createdAtSince) && empty($query->createdAtTill)) {
            $createdSql = ' AND o.created_at >= :createdAtSince';
        } elseif (!empty($query->createdAtSince) && empty($query->createdAtTill)) {
            $createdSql = ' AND o.created_at <= :createdAtTill';
        }

        $spec = new ViewSupplierProductSpecification();

        $baseSql = '
            FROM
                order_doc AS o
                JOIN client_order AS ci ON ci.order_id = o.number
                JOIN order_item AS oi ON oi.order_id = o.number
                JOIN client_order_item AS coi ON coi.order_item_id = oi.id
                JOIN (
                    SELECT
                        oi.id,
                        gnr.delta AS quantity,
                        CASE WHEN sp.product_availability_code < :available 
                            THEN :lack :: order_item_status_code 
                            ELSE :created :: order_item_status_code 
                        END AS status,
                        bp.supplier_id,
                        bp.supplier_price AS purchase_price
                    FROM
                        get_goods_need_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP ) AS gnr
                        JOIN order_item AS oi ON oi.id = gnr.order_item_id
                        JOIN base_product AS bp ON bp.id = oi.base_product_id
                        JOIN supplier_product sp ON sp.base_product_id = bp.id
                        LEFT JOIN supplier_product sp2 ON sp2.base_product_id = sp.base_product_id 
                            AND (sp2.product_availability_code > sp.product_availability_code 
                            OR sp2.product_availability_code = sp.product_availability_code AND sp2.price < sp.price 
                            OR sp2.product_availability_code = sp.product_availability_code AND sp2.price = sp.price AND sp2.id > sp.id)
                    WHERE sp2.id IS NULL
                    '.($query->suppliers ? ' AND bp.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'
                        
                    UNION ALL
                    
                    SELECT
                        oi.id,
                        SUM( oai.quantity ) AS quantity,
                        CASE WHEN oa.is_client_offender = TRUE 
                            THEN :annulled :: order_item_status_code 
                            ELSE :canceled :: order_item_status_code 
                        END AS STATUS,
                        bp.supplier_id,
                        bp.supplier_price AS purchase_price
                    FROM
                        order_annul_item AS oai
                        JOIN order_item AS oi ON oi.id = oai.order_item_id
                        JOIN base_product AS bp ON bp.id = oi.base_product_id
                        JOIN order_annul AS oa ON oa.id = oai.order_annul_id 
                    WHERE
                        oa.is_client_offender = TRUE 
                    '.($query->suppliers ? ' AND bp.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'                        
                    GROUP BY
                        oi.id,
                        oa.is_client_offender,
                        bp.supplier_id,
                        bp.supplier_price
                    HAVING
                        SUM( oai.quantity ) > 0 
                    
                    UNION ALL
                    
                    SELECT
                        oi.id,
                        SUM( srr.delta ) AS quantity,
                        CASE WHEN coi.required_prepayment > coi.reserved_prepayment 
                            THEN :prepayable :: order_item_status_code 
                            WHEN coi.is_clarification_needed = TRUE 
                            THEN :callable :: order_item_status_code 
                            ELSE :shipping :: order_item_status_code 
                        END AS status,
                        srr.supplier_id,
                        srr.purchase_price
                    FROM
                        supplier_reserve_register AS srr
                        JOIN order_item AS oi ON oi.id = srr.order_item_id
                        JOIN client_order_item AS coi ON coi.order_item_id = oi.id
                        JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                        '.($query->suppliers ? ' WHERE srr.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'
                    GROUP BY
                        oi.id,
                        coi.order_item_id,
                        srr.supplier_id,
                        srr.purchase_price
                    HAVING
                        SUM( srr.delta ) > 0 
                    
                    UNION ALL
                    
                    SELECT
                        oi.id,
                        grr.delta AS quantity,
                        CASE WHEN grr.geo_room_id > 0 AND gr.geo_point_id = o.geo_point_id 
                            THEN :releasable :: order_item_status_code 
                            WHEN grr.goods_release_id > 0 AND gre.goods_release_type = :type_transit OR grr.geo_room_id IS NULL AND grr.goods_release_id IS NULL 
                            THEN :transit :: order_item_status_code 
                            ELSE :stationed :: order_item_status_code 
                        END AS status,
                        s.supplier_id,
                        si.purchase_price
                    FROM
                        goods_reserve_register_current AS grr
                        JOIN order_item AS oi ON oi.id = grr.order_item_id
                        JOIN order_doc AS o ON o.number = oi.order_id
                        LEFT JOIN geo_room AS gr ON gr.id = grr.geo_room_id
                        LEFT JOIN goods_release_doc AS gre ON gre.number = grr.goods_release_id
                        LEFT JOIN supply_item AS si ON si.id = grr.supply_item_id
                        LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                            AND si.parent_doc_id = s.id 
                        WHERE grr.goods_condition_code = :reserved
                        '.($query->suppliers ? ' AND s.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'
                    
                    UNION ALL
                    SELECT
                        oi.id,
                        oi.quantity,
                        CASE WHEN d.type = :delivery_courier 
                            THEN :courier :: order_item_status_code 
                            WHEN d.type = :delivery_posting 
                            THEN :post :: order_item_status_code 
                            ELSE :transport :: order_item_status_code 
                        END AS status,
                        112 AS supplier_id ,
                        100 AS purchase_price
                    FROM
                        delivery AS d
                        JOIN delivery_item AS di ON di.delivery_id = d.id
                        JOIN order_delivery AS od ON od.id = di.order_delivery_id
                        JOIN order_delivery_item AS odi ON odi.order_delivery_id = od.id
                        JOIN order_item AS oi ON oi.id = odi.order_item_id
                        JOIN goods_release_doc AS gr ON gr.parent_doc_type = :delivery 
                            AND gr.parent_doc_id = d.id
                        JOIN goods_reserve_register AS grr ON grr.registrator_id = gr.number AND grr.registrator_type_code = \'goods_release\'  AND oi.id = grr.order_item_id
                        ' . /*LEFT JOIN supply_item AS si ON odi.supply_item_ids @> si.ID :: TEXT :: jsonb 
                        LEFT JOIN supply AS s ON si.parent_doc_type = :supply 
                            AND si.parent_doc_id = s.id */
                    'WHERE
                        d.type = :delivery_courier 
                        AND d.reported_at IS NULL 
                        './*($query->suppliers ? ' AND s.supplier_id IN ('.implode(',', $query->suppliers).')' : '').*/'
                        
                    UNION ALL
                    SELECT
                        gid.order_item_id AS id,
                        gid.quantity,
                        :issued :: order_item_status_code AS type,
                        s.supplier_id,
                        si.purchase_price
                    FROM
                        goods_issue_doc AS gid
                        LEFT JOIN supply_item AS si ON si.id = gid.supply_item_id
                        LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                            AND si.parent_doc_id = s.id
                    WHERE
                        gid.order_item_id > 0 
                        AND gid.completed_at IS NULL
                        '.($query->suppliers ? ' AND s.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'
                    UNION ALL
                    SELECT
                        sr.order_item_id AS id,
                        SUM(sr.delta) AS quantity,
                        :completed :: order_item_status_code AS type,
                        s.supplier_id,
                        si.purchase_price
                    FROM
                        sales_register AS sr
                        LEFT JOIN supply_item AS si ON si.id = sr.supply_item_id
                        LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                            AND si.parent_doc_id = s.id
                    WHERE
                        sr.order_item_id > 0 
                        '.($query->suppliers ? ' AND s.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'
                    GROUP BY
                        sr.order_item_id,
                        s.supplier_id,
                        si.id
                    UNION ALL
                    SELECT
                        sr.order_item_id AS id,
                        SUM(-sr.delta) AS quantity,
                        :refunded :: order_item_status_code AS type,
                        s.supplier_id,
                        si.purchase_price
                    FROM
                        sales_register AS sr
                        LEFT JOIN supply_item AS si ON si.id = sr.supply_item_id
                        LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                            AND si.parent_doc_id = s.id
                    WHERE
                        sr.order_item_id > 0 
                        AND sr.delta < 0
                        '.($query->suppliers ? ' AND s.supplier_id IN ('.implode(',', $query->suppliers).')' : '').'
                    GROUP BY
                        sr.order_item_id,
                        s.supplier_id,
                        si.id
                ) AS oibs ON oi.id = oibs.id
                JOIN base_product AS bp ON bp.id = oi.base_product_id
                JOIN order_item_status AS ois ON ois.code = oibs.status 
                JOIN geo_city AS gc ON gc.id = o.geo_city_id
                LEFT JOIN payment_type AS pt ON pt.code = ci.payment_type_code
                LEFT JOIN "user" AS u ON u.id = ci.user_id
                LEFT JOIN person AS p ON u.person_id = p.id
                LEFT JOIN supplier AS s ON s.id = oibs.supplier_id
            WHERE
                1 = 1
                '.(!empty($query->id) ? ' AND o.number = :id' : '').'
                '.$createdSql.'  
                '.(!empty($query->paymentType) ? ' AND ci.payment_type_code = :paymentType' : '').'  
                '.(!empty($query->cities) ? ' AND o.geo_city_id IN ( '.implode(',', $query->cities).' )' : '').'  
                '.(!empty($query->channel) ? ' AND o.type_code = :channel' : '').'
                '.(!empty($query->deliveryType) ? ' AND ci.delivery_type_code = :deliveryType' : '').'
                '.(!empty($query->statuses) ? ' AND ois.code IN ( \''.implode('\',\'', $query->statuses).'\' )' : '').'
                '.(!empty($query->managerId) ? ' AND o.manager_id = :managerId' : '').'
                '.(!empty($query->clientId) ? ' AND ci.user_id = :clientId' : '').'
                '.(!empty($query->createdFrom) ? ' AND oi.created_at >= :createdFrom' : '').'
                '.(!empty($query->createdTo) ? ' AND oi.created_at <= :createdTo' : '').'
                '.($query->special === self::SPECIAL_1 ? ' AND ci.is_not_reached = TRUE' : '').'
                '.($query->special === self::SPECIAL_3 ? ' AND coi.is_clarification_needed = TRUE' : '').'
                '.($query->special === self::SPECIAL_2 ? ' AND coi.required_prepayment - coi.reserved_prepayment > 0' : '').'
        ';
        $sql = '
            SELECT
                COUNT(DISTINCT o.number) AS total
            ' . $baseSql . '
        ';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total', 'integer');
        $q = $this->getDoctrine()->getManager()->createNativeQuery($sql, $rsm);

        $q->setParameter('id', $query->id);
        $q->setParameter('available', 'available');
        $q->setParameter('completed', OrderItemStatusCode::COMPLETED);
        $q->setParameter('refunded', OrderItemStatusCode::REFUNDED);
        $q->setParameter('lack', OrderItemStatusCode::LACK);
        $q->setParameter('created', OrderItemStatusCode::CREATED);
        $q->setParameter('annulled', OrderItemStatusCode::ANNULLED);
        $q->setParameter('canceled', OrderItemStatusCode::CANCELED);
        $q->setParameter('prepayable', OrderItemStatusCode::PREPAYABLE);
        $q->setParameter('callable', OrderItemStatusCode::CALLABLE);
        $q->setParameter('shipping', OrderItemStatusCode::SHIPPING);
        $q->setParameter('releasable', OrderItemStatusCode::RELEASABLE);
        $q->setParameter('transit', OrderItemStatusCode::TRANSIT);
        $q->setParameter('stationed', OrderItemStatusCode::STATIONED);
        $q->setParameter('courier', OrderItemStatusCode::COURIER);
        $q->setParameter('post', OrderItemStatusCode::POST);
        $q->setParameter('transport', OrderItemStatusCode::TRANSPORT);
        $q->setParameter('issued', OrderItemStatusCode::ISSUED);
        $q->setParameter('supply', DocumentTypeCode::SUPPLY);
        $q->setParameter('delivery', DocumentTypeCode::DELIVERY);
        $q->setParameter('delivery_courier', DeliveryTypeCode::COURIER);
        $q->setParameter('delivery_posting', DeliveryTypeCode::POSTING);
        $q->setParameter('type_transit', GoodsReleaseType::TRANSIT);
        $q->setParameter('reserved', GoodsConditionCode::RESERVED);
        $q->setParameter('paymentType', $query->paymentType);
        $q->setParameter('channel', $query->channel ? constant('self::' . strtoupper($query->channel)) : '');
        $q->setParameter('deliveryType', $query->deliveryType);
        $q->setParameter('managerId', $query->managerId);
        $q->setParameter('clientId', $query->clientId);
        $q->setParameter('createdFrom', $query->createdFrom);
        $q->setParameter('createdTo', $query->createdTo);

        $total = $q->getSingleScalarResult();

        $sql = "
            SELECT
                o.number AS id,
                gc.name AS city_name,
                ci.user_id,
                CONCAT_WS(' ', p.lastname, p.firstname, p.secondname) AS user_name,
                pt.abbreviation AS payment_type_abbreviation,
                pt.name AS payment_type_name,
                (SELECT CONCAT_WS(' ', p.lastname, p.firstname, p.secondname) FROM person AS p JOIN \"user\" AS u ON u.person_id = p.id WHERE u.id = o.manager_id) AS manager_name,
                o.created_at,
                (SELECT name FROM counteragent WHERE id = o.our_seller_counteragent_id) AS seller_counteragent_name,
                (
                    SELECT
                        CONCAT( '[', string_agg ( CONCAT ( '{\"type\":\"', contact_type_code, '\",\"value\":\"', VALUE, '\"}' ), ',' ), ']' ) 
                    FROM
                        contact 
                    WHERE
                        person_id = p.id 
                        AND contact_type_code IN ('phone', 'mobile', 'email')
                ) AS contacts,
                (SELECT COUNT(id) FROM order_comment WHERE order_id = o.number AND order_item_id IS NULL) AS comments_count,
                ci.is_not_reached,
                SUM(COALESCE(coi.franchiser_client_price, coi.retail_price) * (oi.quantity - COALESCE((SELECT SUM(quantity) FROM order_annul_item WHERE order_item_id = oi.id), 0))) AS order_sum,
                CONCAT( '[', string_agg ( DISTINCT oi.id::text, ',' ), ']' ) AS items_ids,
                0 AS prepayment_amount
            {$baseSql}
            GROUP BY o.did, gc.id, pt.code, ci.order_id, p.id
            ORDER BY o.created_at DESC
            LIMIT {$query->limit} 
            OFFSET " . (($query->page - 1) * $query->limit) . "
        ";

        $q = $em->createNativeQuery($sql, new DTORSM(\OrderBundle\Bus\Data\Query\DTO\Order::class));

        $q->setParameter('id', $query->id);
        $q->setParameter('available', 'available');
        $q->setParameter('completed', OrderItemStatusCode::COMPLETED);
        $q->setParameter('refunded', OrderItemStatusCode::REFUNDED);
        $q->setParameter('lack', OrderItemStatusCode::LACK);
        $q->setParameter('created', OrderItemStatusCode::CREATED);
        $q->setParameter('annulled', OrderItemStatusCode::ANNULLED);
        $q->setParameter('canceled', OrderItemStatusCode::CANCELED);
        $q->setParameter('prepayable', OrderItemStatusCode::PREPAYABLE);
        $q->setParameter('callable', OrderItemStatusCode::CALLABLE);
        $q->setParameter('shipping', OrderItemStatusCode::SHIPPING);
        $q->setParameter('releasable', OrderItemStatusCode::RELEASABLE);
        $q->setParameter('transit', OrderItemStatusCode::TRANSIT);
        $q->setParameter('stationed', OrderItemStatusCode::STATIONED);
        $q->setParameter('courier', OrderItemStatusCode::COURIER);
        $q->setParameter('post', OrderItemStatusCode::POST);
        $q->setParameter('transport', OrderItemStatusCode::TRANSPORT);
        $q->setParameter('issued', OrderItemStatusCode::ISSUED);
        $q->setParameter('supply', DocumentTypeCode::SUPPLY);
        $q->setParameter('delivery', DocumentTypeCode::DELIVERY);
        $q->setParameter('delivery_courier', DeliveryTypeCode::COURIER);
        $q->setParameter('delivery_posting', DeliveryTypeCode::POSTING);
        $q->setParameter('type_transit', GoodsReleaseType::TRANSIT);
        $q->setParameter('reserved', GoodsConditionCode::RESERVED);
        $q->setParameter('paymentType', $query->paymentType);
        $q->setParameter('channel', $query->channel ? constant('self::' . strtoupper($query->channel)) : '');
        $q->setParameter('deliveryType', $query->deliveryType);
        $q->setParameter('managerId', $query->managerId);
        $q->setParameter('clientId', $query->clientId);
        $q->setParameter('createdFrom', $query->createdFrom);
        $q->setParameter('createdTo', $query->createdTo);

        $orders = [];
        foreach ($q->getResult('DTOHydrator') as $order) {
            $order->contacts = json_decode($order->contacts, true);
            $order->itemsIds = json_decode($order->itemsIds, true);
            $orders[$order->id] = $order;
        }
        
        $sql = "
            SELECT
                oi.id,
                oi.order_id AS order_id,
                oibs.quantity,
                oibs.status AS status_code, 
                bp.name AS product_name, 
                oi.base_product_id, 
                (SELECT COUNT(id) FROM order_comment WHERE order_item_id = oi.id) AS comments_count, 
                COALESCE(coi.franchiser_client_price, coi.retail_price) AS retail_price, 
                oibs.purchase_price,
                s.code AS supplier_code, 
                NOW() AS delivery_date, 
                (SELECT CONCAT_WS(' ', p.lastname, p.firstname, p.secondname) FROM person AS p JOIN \"user\" AS u ON u.person_id = p.id JOIN org_employee AS e ON e.user_id = u.id WHERE u.id = oi.created_by) AS manager_name, 
                (SELECT SUM(delta) FROM goods_reserve_register_current WHERE base_product_id = bp.id AND goods_condition_code = 'free') AS reserves_count
            FROM order_item AS oi
            JOIN (
                SELECT
                    oi.id,
                    gnr.delta AS quantity,
                    CASE WHEN sp.product_availability_code < :available 
                        THEN :lack :: order_item_status_code 
                        ELSE :created :: order_item_status_code 
                    END AS status,
                    bp.supplier_id,
                    bp.supplier_price AS purchase_price
                FROM
                    get_goods_need_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP ) AS gnr
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN base_product AS bp ON bp.id = oi.base_product_id
                    JOIN supplier_product sp ON sp.base_product_id = bp.id
                    LEFT JOIN supplier_product sp2 ON sp2.base_product_id = sp.base_product_id 
                        AND (sp2.product_availability_code > sp.product_availability_code 
                        OR sp2.product_availability_code = sp.product_availability_code AND sp2.price < sp.price 
                        OR sp2.product_availability_code = sp.product_availability_code AND sp2.price = sp.price AND sp2.id > sp.id)
                WHERE sp2.id IS NULL
                    
                UNION ALL
                
                SELECT
                    oi.id,
                    SUM( oai.quantity ) AS quantity,
                    CASE WHEN oa.is_client_offender = TRUE 
                        THEN :annulled :: order_item_status_code 
                        ELSE :canceled :: order_item_status_code 
                    END AS STATUS,
                    bp.supplier_id,
                    bp.supplier_price AS purchase_price
                FROM
                    order_annul_item AS oai
                    JOIN order_item AS oi ON oi.id = oai.order_item_id
                    JOIN base_product AS bp ON bp.id = oi.base_product_id
                    JOIN order_annul AS oa ON oa.id = oai.order_annul_id 
                WHERE
                    oa.is_client_offender = TRUE                       
                GROUP BY
                    oi.id,
                    oa.is_client_offender,
                    bp.supplier_id,
                    bp.supplier_price
                HAVING
                    SUM( oai.quantity ) > 0 
                
                UNION ALL
                
                SELECT
                    oi.id,
                    SUM( srr.delta ) AS quantity,
                    CASE WHEN coi.required_prepayment > coi.reserved_prepayment 
                        THEN :prepayable :: order_item_status_code 
                        WHEN coi.is_clarification_needed = TRUE 
                        THEN :callable :: order_item_status_code 
                        ELSE :shipping :: order_item_status_code 
                    END AS status,
                    srr.supplier_id,
                    srr.purchase_price
                FROM
                    supplier_reserve_register AS srr
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN client_order_item AS coi ON coi.order_item_id = oi.id
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                GROUP BY
                    oi.id,
                    coi.order_item_id,
                    srr.supplier_id,
                    srr.purchase_price
                HAVING
                    SUM( srr.delta ) > 0 
                
                UNION ALL
                
                SELECT
                    oi.id,
                    grr.delta AS quantity,
                    CASE WHEN grr.geo_room_id > 0 AND gr.geo_point_id = o.geo_point_id 
                        THEN :releasable :: order_item_status_code 
                        WHEN grr.goods_release_id > 0 AND gre.goods_release_type = :type_transit OR grr.geo_room_id IS NULL AND grr.goods_release_id IS NULL 
                        THEN :transit :: order_item_status_code 
                        ELSE :stationed :: order_item_status_code 
                    END AS status,
                    s.supplier_id,
                    si.purchase_price
                FROM
                    goods_reserve_register_current AS grr
                    JOIN order_item AS oi ON oi.id = grr.order_item_id
                    JOIN order_doc AS o ON o.number = oi.order_id
                    LEFT JOIN geo_room AS gr ON gr.id = grr.geo_room_id
                    LEFT JOIN goods_release_doc AS gre ON gre.number = grr.goods_release_id
                    LEFT JOIN supply_item AS si ON si.id = grr.supply_item_id
                    LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                        AND si.parent_doc_id = s.id 
                    WHERE grr.goods_condition_code = :reserved
                
                UNION ALL
                SELECT
                    oi.id,
                    oi.quantity,
                    CASE WHEN d.type = :delivery_courier 
                        THEN :courier :: order_item_status_code 
                        WHEN d.type = :delivery_posting 
                        THEN :post :: order_item_status_code 
                        ELSE :transport :: order_item_status_code 
                    END AS status,
                    112 AS supplier_id ,
                    100 AS purchase_price
                FROM
                    delivery AS d
                    JOIN delivery_item AS di ON di.delivery_id = d.id
                    JOIN order_delivery AS od ON od.id = di.order_delivery_id
                    JOIN order_delivery_item AS odi ON odi.order_delivery_id = od.id
                    JOIN order_item AS oi ON oi.id = odi.order_item_id
                    LEFT JOIN goods_release_doc AS gr ON gr.parent_doc_type = :delivery 
                        AND gr.parent_doc_id = d.id
                WHERE
                    d.type = :delivery_courier 
                    AND d.reported_at IS NULL 
                    
                UNION ALL
                SELECT
                    gid.order_item_id AS id,
                    gid.quantity,
                    :issued :: order_item_status_code AS type,
                    s.supplier_id,
                    si.purchase_price
                FROM
                    goods_issue_doc AS gid
                    LEFT JOIN supply_item AS si ON si.id = gid.supply_item_id
                    LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                        AND si.parent_doc_id = s.id
                WHERE
                    gid.order_item_id > 0 
                    AND gid.completed_at IS NULL
                UNION ALL
                SELECT
                    sr.order_item_id AS id,
                    SUM(sr.delta) AS quantity,
                    :completed :: order_item_status_code AS type,
                    s.supplier_id,
                    si.purchase_price
                FROM
                    sales_register AS sr
                    LEFT JOIN supply_item AS si ON si.id = sr.supply_item_id
                    LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                        AND si.parent_doc_id = s.id
                WHERE
                    sr.order_item_id > 0 
                GROUP BY
                    sr.order_item_id,
                    s.supplier_id,
                    si.id
                UNION ALL
                SELECT
                    sr.order_item_id AS id,
                    SUM(-sr.delta) AS quantity,
                    :refunded :: order_item_status_code AS type,
                    s.supplier_id,
                    si.purchase_price
                FROM
                    sales_register AS sr
                    LEFT JOIN supply_item AS si ON si.id = sr.supply_item_id
                    LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                        AND si.parent_doc_id = s.id
                WHERE
                    sr.order_item_id > 0 
                    AND sr.delta < 0
                GROUP BY
                    sr.order_item_id,
                    s.supplier_id,
                    si.id
            ) AS oibs ON oi.id = oibs.id
            JOIN base_product AS bp ON bp.id = oi.base_product_id
            JOIN client_order_item AS coi ON coi.order_item_id = oi.id
            LEFT JOIN supplier AS s ON s.id = oibs.supplier_id
            WHERE oi.order_id IN (:ordersIds)
            ORDER BY oi.created_at DESC
        ";

        $q = $em->createNativeQuery($sql, new DTORSM(\OrderBundle\Bus\Data\Query\DTO\OrderItem::class));

        $q->setParameter('id', $query->id);
        $q->setParameter('available', 'available');
        $q->setParameter('completed', OrderItemStatusCode::COMPLETED);
        $q->setParameter('refunded', OrderItemStatusCode::REFUNDED);
        $q->setParameter('lack', OrderItemStatusCode::LACK);
        $q->setParameter('created', OrderItemStatusCode::CREATED);
        $q->setParameter('annulled', OrderItemStatusCode::ANNULLED);
        $q->setParameter('canceled', OrderItemStatusCode::CANCELED);
        $q->setParameter('prepayable', OrderItemStatusCode::PREPAYABLE);
        $q->setParameter('callable', OrderItemStatusCode::CALLABLE);
        $q->setParameter('shipping', OrderItemStatusCode::SHIPPING);
        $q->setParameter('releasable', OrderItemStatusCode::RELEASABLE);
        $q->setParameter('transit', OrderItemStatusCode::TRANSIT);
        $q->setParameter('stationed', OrderItemStatusCode::STATIONED);
        $q->setParameter('courier', OrderItemStatusCode::COURIER);
        $q->setParameter('post', OrderItemStatusCode::POST);
        $q->setParameter('transport', OrderItemStatusCode::TRANSPORT);
        $q->setParameter('issued', OrderItemStatusCode::ISSUED);
        $q->setParameter('supply', DocumentTypeCode::SUPPLY);
        $q->setParameter('delivery', DocumentTypeCode::DELIVERY);
        $q->setParameter('delivery_courier', DeliveryTypeCode::COURIER);
        $q->setParameter('delivery_posting', DeliveryTypeCode::POSTING);
        $q->setParameter('type_transit', GoodsReleaseType::TRANSIT);
        $q->setParameter('reserved', GoodsConditionCode::RESERVED);
        $q->setParameter('paymentType', $query->paymentType);
        $q->setParameter('channel', $query->channel ? constant('self::' . strtoupper($query->channel)) : '');
        $q->setParameter('deliveryType', $query->deliveryType);
        $q->setParameter('managerId', $query->managerId);
        $q->setParameter('clientId', $query->clientId);
        $q->setParameter('createdFrom', $query->createdFrom);
        $q->setParameter('createdTo', $query->createdTo);
        $q->setParameter('ordersIds', array_keys($orders));

        foreach ($q->getResult('DTOHydrator') as $orderItem) {
            $orderItem->isFiltered = in_array($orderItem->id, $orders[$orderItem->orderId]->itemsIds);
            $orders[$orderItem->orderId]->items[] = $orderItem;
        }

        return new DTO\Items(array_values($orders), $total);
    }
}
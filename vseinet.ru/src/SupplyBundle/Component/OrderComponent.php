<?php

namespace SupplyBundle\Component;

use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\SupplierReserveStatus;
use AppBundle\Specification\ViewSupplierProductSpecification;
use ContentBundle\Entity\BaseProductImage;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsReserveType;
use ContentBundle\Repository\BaseProductImageRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use OrderBundle\Entity\OrderItem;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierReserveItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;

class OrderComponent
{
    const TYPE_ORDER = 'order';
    const TYPE_REQUEST = 'request';

    const STATE_WAYBILL = 'waybill';
    const STATE_TRANSIT = 'transit';
    const STATE_FORMING = 'forming';

    private $_orhCategory = [
        '08. ДВЕРИ' => true,
        '19. ШВЕЙНЫЕ ИЗДЕЛИЯ "ДОБРОШВЕЙКИН"' => true,
        '05. ЗАМКИ, ПЕТЛИ, СКОБЯНЫЕ ИЗДЕЛИЯ' => true,
        '06. ИНСТРУМЕНТ РУЧНОЙ' => true,
        '07. ЛАКОКРАСОЧНАЯ ПРОДУКЦИЯ' => true,
        '02. ОТДЕЛОЧНЫЕ МАТЕРИАЛЫ' => true,
        '11. ПОГОНАЖНЫЕ ИЗДЕЛИЯ ИЗ ДЕРЕВА' => true,
        '10. САНКИ' => true,
        '03. САНТЕХНИКА' => true,
        '04. САДОВО-ОГОРОДНЫЙ ИНВЕНТАРЬ' => true,
        '09. СПЕЦОДЕЖДА, ПЕРЧАТКИ, РУКАВИЦЫ' => true,
        '01. СТРОИТЕЛЬНЫЕ МАТЕРИАЛЫ' => true,
        '12. ВЕШАЛКИ' => true,
    ];

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

    /**
     * Вычисление цены со скидкой и бонусами для конкретного поставщика
     *
     * @param int $supplierID
     * @param int $price
     * @param string $category
     *
     * @return int
     */
    public function calcSupplierPrice(int $supplierID, int $price, string $category) : int
    {
        switch ($supplierID) {
            case Supplier::ME:
                $price *= 0.95;
                break;
            case Supplier::ORH:
                if (isset($this->_orhCategory[$category])) {
                    $price *= 0.96;
                } else {
                    $price *= 0.94;
                }
                break;
            case Supplier::OR:
                $price *= 0.98;
                break;
            case Supplier::TW:
                $price *= 0.93;
                break;
            default:
                $price = $price * 1;
        }

        return $price;
    }

    /**
     * Получение позиций для выгрузки в файлы по поставщику
     *
     * @param int  $supplierId
     * @param int  $pointId
     * @param bool $isWithConfirmedReserves
     *
     * @return array
     */
    public function getDownloadingProducts(int $supplierId, $pointId = 0, $isWithConfirmedReserves = false) : array
    {
        $geoPointSQL = '';

        $spec = new ViewSupplierProductSpecification();

        if (!empty($pointId)) {
            $geoPointSQL = ' AND o.geo_point_id = :geo_point_id';
        }
        $sql = '
            SELECT 
                COALESCE ( sp.name, bp.name ) as name /*наименование товара у поставщика*/,
                SUM ( gnr.quantity ) :: INTEGER AS quantity /*требуемое количество*/,
                sp.code /*артикул товара у поставщика*/,
                round(SUM ( bp.supplier_price * gnr.quantity ) / SUM ( gnr.quantity )) :: INTEGER AS price /*цена закупки*/
            FROM (
                SELECT 
                    SUM( gnr.delta ) AS quantity,
                    gnr.base_product_id,
                    bp.supplier_price AS purchase_price 
                FROM
                    get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP) AS gnr
                    JOIN base_product AS bp ON bp.id = gnr.base_product_id
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    bp.supplier_id = :supplier_id 
                    '.$geoPointSQL.' 
                GROUP BY
                    gnr.base_product_id,
                    bp.supplier_price 
                '.($isWithConfirmedReserves ? '                    
                UNION ALL
                SELECT 
                    SUM( srr.delta ) AS quantity,
                    srr.base_product_id,
                    srr.purchase_price 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    sr.supplier_id = :supplier_id 
                    '.$geoPointSQL.'  
                GROUP BY
                    srr.base_product_id,
                    srr.purchase_price 
                HAVING
                    SUM ( srr.delta ) > 0 
                ' : '').'
                ) AS gnr
                JOIN base_product AS bp ON gnr.base_product_id = bp.id 
                '.$spec->buildLeftJoin('bp.id', $supplierId).'
            WHERE '.$spec->buildWhere(false).'
            GROUP BY
                sp."name",
                bp.name,
                sp.code 
            ORDER BY
                name
        ';

        $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('supplier_id', $supplierId);
        if (!empty($pointId)) {
            $query->setParameter('geo_point_id', $pointId);
        }

        $supplierProducts = $query->getResult('ListAssocHydrator');

        // Очищаем предыдущую выгрузку
        $statement = $this->getEm()->getConnection()->prepare('
            DELETE  FROM supplier_reserve_request WHERE supplier_id = :supplier_id
        ');
        $statement->bindValue('supplier_id', $supplierId);
        $statement->execute();

        // Помечаем позиции заказа, как выгруженные
        $statement = $this->getEm()->getConnection()->prepare('
            INSERT INTO supplier_reserve_request ( order_item_id, supplier_id, quantity ) 
            SELECT
                gnr.order_item_id,
                bp.supplier_id,
                gnr.delta 
            FROM
                get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP) AS gnr
                JOIN order_item AS oi ON oi.id = gnr.order_item_id
                JOIN base_product AS bp ON bp.id = oi.base_product_id
                JOIN "order" AS o ON o.id = oi.order_id 
            WHERE
                gnr.order_item_id IS NOT NULL 
                AND bp.supplier_id = :supplier_id 
            '.$geoPointSQL
        );
        $statement->bindValue('supplier_id', $supplierId);
        if (!empty($pointId)) {
            $statement->bindValue('geo_point_id', $pointId);
        }
        $statement->execute();

        return $supplierProducts;
    }

    /**
     * Получение списка позиций по поставщику
     *
     * @param int $supplierReserveID
     * @param string $webPath
     * @param int $pointId
     * @param bool $isWithConfirmedReserves
     *
     * @return array
     */
    public function getSupplierProducts(
        int $supplierReserveID,
        string $webPath,
        $pointId = 0,
        $isWithConfirmedReserves = true) : array
    {
        $geoPointSql = $pointId > 0 ? ' AND o.geo_point_id = :geo_point_id' : '';

        $spec = new ViewSupplierProductSpecification();

        // Список товаров
        $sql = '
            SELECT
                bp.id /*ид товара*/,
                sp.code /*код товара у поставщика*/,
                COALESCE ( sp.name, bp.name ) as name /*наименование товара у поставщика*/,
                bpi.basename AS photo_url /*путь к фото товара*/,
                SUM ( gnr.delta ) :: INTEGER AS need_quantity /*требуемое количество товара*/
            FROM (
                SELECT
                  gnr.delta,
                  gnr.base_product_id 
                FROM
                    get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP) AS gnr 
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                    JOIN base_product AS bp ON bp.id = oi.base_product_id
                    JOIN supplier_reserve AS sr ON sr.supplier_id = bp.supplier_id
                WHERE
                  sr.id = :id '.$geoPointSql.' 
                
                '.($isWithConfirmedReserves ? '
                UNION ALL
                
                SELECT 
                    SUM ( srr.delta ),
                    srr.base_product_id 
                FROM
                    supplier_reserve_register AS srr
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                WHERE
                    sr.id = :id 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL '.$geoPointSql.' 
                GROUP BY
                    srr.base_product_id 
                HAVING
                    SUM ( srr.delta ) > 0 ' : '') . '  
                    ) AS gnr
                JOIN base_product AS bp ON gnr.base_product_id = bp.id 
                '.$spec->buildLeftJoin('bp.id', 'bp.supplier_id').'
                LEFT JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id 
                    AND bpi.sort_order = 1 
            WHERE '.$spec->buildWhere(false).'
            GROUP BY
                sp.code,
                sp.name,
                bp.id,
                bpi.basename 
            HAVING
                SUM ( gnr.delta ) > 0 
            ORDER BY
                name
        ';

        $query = $this->getEm()->createNativeQuery($sql, new DTORSM(\SupplyBundle\Bus\Order\Query\DTO\OrderProducts::class));
        $query->setParameter('id', $supplierReserveID);
        $query->setParameter('geo_point_id', $pointId);

        $products = $query->getResult('DTOHydrator');

        foreach ($products as &$product) {
            $product->photoUrl = $this->getEm()->getRepository(BaseProductImage::class)->buildSrc($webPath, $product->photoUrl, BaseProductImageRepository::SIZE_XS);
        }

        // Список заказов
        $sql = '
            SELECT
                CONCAT_WS ( \'_\', oi.id, CASE WHEN gnr.reserved_quantity > 0 THEN gnr.purchase_price ELSE NULL END ) AS id /*ид*/,
                bp.id AS base_product_id /*ид товара*/,
                oi.id AS order_item_id /*номер позиции*/,
                oi.order_id /*номер заказа*/,
                gnr.purchase_price /*цена закупки*/,
                coi.retail_price /*цена продажи*/,
                gnr.need_quantity :: INTEGER /*требуемое количество*/,
                gnr.reserved_quantity :: INTEGER /*зарезервированное количество*/,
                vup.fullname AS client_name /*имя клиента*/,
                CASE WHEN co.user_id > 0 THEN
                    CONCAT_WS (\', \', NULLIF ( vup.mobile, \'\' ),
                    NULLIF ( vup.phone, \'\' )) ELSE NULL 
                END AS phones /*телефоны*/,
                gc.name as city /**/,
                CASE WHEN (
                    SELECT 
                        SUM( delta ) 
                    FROM
                        get_goods_reserve_register_data( CURRENT_TIMESTAMP :: TIMESTAMP, oi.base_product_id, 0, :free, 0, NULL ) 
                        ) > 0 THEN
                    TRUE ELSE FALSE 
                END AS has_available_reserve /*можно зарезервировать с наличия*/,
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
                JOIN (
                    SELECT
                        gnr.delta AS need_quantity,
                        NULL :: INTEGER AS reserved_quantity,
                        gnr.order_item_id,
                        bp.supplier_price AS purchase_price 
                    FROM
                        get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP) AS gnr 
                        JOIN order_item AS oi ON oi.id = gnr.order_item_id
                        JOIN "order" AS o ON o.id = oi.order_id 
                        JOIN base_product AS bp ON bp.id = oi.base_product_id
                        JOIN supplier_reserve AS sr ON sr.supplier_id = bp.supplier_id
                    WHERE
                      sr.id = :id '.$geoPointSql.'  
                        
                    '.($isWithConfirmedReserves ? '
                    UNION ALL
                    
                    SELECT SUM
                        ( srr.delta ) AS need_quantity,
                        SUM ( srr.delta ) AS reserved_quantity,
                        srr.order_item_id,
                        srr.purchase_price 
                    FROM
                        supplier_reserve_register AS srr
                        JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                        JOIN order_item AS oi ON oi.id = srr.order_item_id
                        JOIN "order" AS o ON o.id = oi.order_id 
                    WHERE
                        sr.id = :id '.$geoPointSql.'
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    GROUP BY
                        srr.order_item_id,
                        srr.purchase_price 
                    HAVING
                        SUM ( srr.delta ) > 0 ' : '') . '   
                ) AS gnr ON oi.id = gnr.order_item_id
                LEFT JOIN client_order_item AS coi ON coi.order_item_id = oi.id 
                JOIN "order" AS o ON o.id = oi.order_id
                JOIN base_product AS bp ON bp.id = oi.base_product_id
                LEFT JOIN client_order AS co ON co.order_id = o.id 
                LEFT JOIN func_view_user_person(COALESCE ( co.user_id, o.manager_id )) AS vup ON vup.user_id = COALESCE ( co.user_id, o.manager_id )
                JOIN geo_city AS gc ON gc.id = o.geo_city_id 
            ORDER BY
                gnr.reserved_quantity,
                CASE WHEN o.type_code IN ( :site, :shop, :legal, :request ) THEN 0 ELSE 1 END,
                o.created_at
        ';

        $query = $this->getEm()->createNativeQuery($sql, new DTORSM(\SupplyBundle\Bus\Order\Query\DTO\OrderItems::class));
        $query->setParameter('free', GoodsConditionCode::FREE);
        $query->setParameter('site', OrderTypeCode::SITE);
        $query->setParameter('shop', OrderTypeCode::SHOP);
        $query->setParameter('legal', OrderTypeCode::LEGAL);
        $query->setParameter('request', OrderTypeCode::RESUPPLY);
        $query->setParameter('id', $supplierReserveID);
        $query->setParameter('geo_point_id', $pointId);

        $orders = $query->getResult('DTOHydrator');

        return ['products' => $products, 'orderItems' => $orders,];
    }

    /**
     * @param int  $supplierID
     * @param bool $isSupplierOnly
     *
     * @return array
     */
    public function getUploadingProducts(int $supplierID, bool $isSupplierOnly) : array
    {
        $supplierSQL = '';

        $spec = new ViewSupplierProductSpecification();

        if (empty($isSupplierOnly)) {
            $supplierSQL = '
                UNION ALL

                SELECT
                    sp.code,
                    COALESCE ( sp.name, base_product.name ) AS name,
                    base_product_image.basename,
                    base_product.id AS base_product_id,a
                    base_product.name AS base_product_name,
                    sp.price,
                    view_supplier_order_item.quantity AS quantity
                FROM
                    view_supplier_order_item
                    JOIN base_product ON base_product.id = view_supplier_order_item.base_product_id
                    LEFT JOIN base_product_image ON base_product_image.base_product_id = base_product.id AND base_product_image.sort_order = 1
                    '.$spec->buildLeftJoin('base_product.id', 'view_supplier_order_item.supplier_id').'
                    JOIN func_view_supplier_product(base_product.id) AS vsp2 ON vsp2.base_product_id = base_product.id 
                    JOIN view_item_to_supplier ON view_item_to_supplier.id = view_supplier_order_item.id
                WHERE
                    view_item_to_supplier.supplier_id = :current_supplier_id
                    AND view_supplier_order_item.supplier_id != :current_supplier_id
                    AND vsp2.supplier_id = :current_supplier_id
                    AND view_supplier_order_item.reserve_status = :processing
                    '.$spec->buildWhere().'
            ';
        }

        // Список товаров
        $sql = "
            SELECT
                T.code,
                T.name,
                T.basename,
                T.base_product_id,
                T.base_product_name,
                T.price,
                SUM(T.quantity) AS quantity
            FROM (
                SELECT
                    sp.code,
                    COALESCE(sp.name, base_product.name) AS name,
                    base_product_image.basename,
                    base_product.id AS base_product_id,
                    base_product.name AS base_product_name,
                    sp.price,
                    view_supplier_order_item.quantity AS quantity
                FROM
                    view_supplier_order_item
                    JOIN base_product ON base_product.id = view_supplier_order_item.base_product_id
                    LEFT JOIN base_product_image ON base_product_image.base_product_id = base_product.id AND base_product_image.sort_order = 1
                    ".$spec->buildLeftJoin('base_product.id', 'view_supplier_order_item.supplier_id')."
                    JOIN view_item_to_supplier ON view_item_to_supplier.id = view_supplier_order_item.id
                WHERE
                    view_item_to_supplier.supplier_id = :current_supplier_id
                    AND view_supplier_order_item.supplier_id = :current_supplier_id
                    AND view_supplier_order_item.reserve_status = :processing
                    {$supplierSQL}
                    ".$spec->buildWhere()."
            ) AS T
            GROUP BY
                T.code,
                T.name,
                T.basename,
                T.base_product_id,
                T.base_product_name,
                T.price
        ";

        $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('current_supplier_id', $supplierID);
        $query->setParameter('processing', SupplierReserveStatus::PROCESSING);

        $products = $query->getResult('ListAssocHydrator');

        // Получение позиций заказа
        $sql = '
            SELECT
                order_item.base_product_id,
                order_item.id,
                order_item.order_id,
                vup.fullname,
                vup.mobile,
                vup.phone,
                geo_city.name AS geo_city,
                order_item.retail_price,
                order_item.quantity,
                CASE WHEN order_item.supplier_reserve = :delayed
                    THEN TRUE
                    ELSE FALSE
                END AS is_delayed,
                CASE WHEN ( SELECT SUM (delta) FROM goods_reserve_log WHERE base_product_id = order_item.base_product_id AND reserve_type = :new AND order_item_id IS NULL ) > 0
                    THEN TRUE
                    ELSE FALSE
                END AS can_be_reserved,
                CASE WHEN (
                    SELECT COUNT(id)
                    FROM
                        order_comment
                    WHERE
                        order_id = order_item.order_id
                        AND order_item_id = COALESCE(order_item.id, order_item.id)) > 0
                    THEN TRUE
                    ELSE FALSE
                END AS has_comments
            FROM
                order_item
                JOIN "order" ON "order".id = order_item.order_id
                LEFT JOIN func_view_user_person("order".user_id) AS vup ON vup.id = "order".user_id
                JOIN geo_city ON geo_city.id = "order".geo_city_id
                JOIN order_item_to_supplier ON order_item_to_supplier.id = order_item.id
            WHERE
                order_item_to_supplier.supplier_id = :current_supplier_id
                AND order_item.supplier_reserve = :processing
            ORDER BY
                CASE WHEN order_item.supplier_id = : current_supplier_id
                  THEN 0
                  ELSE 1
                END,
                order_item.created_at
        ';

        $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('delayed', SupplierReserveStatus::DELAYED);
        $query->setParameter('new', GoodsReserveType::NEW);
        $query->setParameter('current_supplier_id', $supplierID);
        $query->setParameter('processing', SupplierReserveStatus::PROCESSING);

        $orders = $query->getResult('ListAssocHydrator');

        // Получение запросов
        $sql = '
            SELECT
                goods_request.base_product_id,
                goods_request.id,
                vup.fullname,
                goods_request.quantity,
                geo_city.name AS geo_city,
                CASE WHEN goods_request.supplier_reserve = :delayed
                    THEN TRUE
                    ELSE FALSE
                END AS is_delayed
            FROM
                goods_request
                LEFT JOIN func_view_user_person(goods_request.created_by) AS vup ON vup.id = goods_request.created_by
                JOIN geo_point ON geo_point.id = goods_request.geo_point_id
                JOIN geo_city ON geo_city.id = geo_point.geo_city_id
                JOIN goods_request_to_supplier ON goods_request_to_supplier.id = goods_request.id
            WHERE
                goods_request_to_supplier.supplier_id = :current_supplier_id
                AND goods_request.supplier_reserve = :processing
            ORDER BY
                CASE WHEN goods_request.supplier_id = :current_supplier_id
                    THEN 0
                    ELSE 1
                END,
                goods_request.created_at
        ';

        $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('delayed', SupplierReserveStatus::DELAYED);
        $query->setParameter('current_supplier_id', $supplierID);
        $query->setParameter('processing', SupplierReserveStatus::PROCESSING);

        $requests = $query->getResult('ListAssocHydrator');

        return ['products' => $products, 'orders' => $orders, 'requests' => $requests,];
    }

    /**
     * @param \ServiceBundle\Services\RegisterService $service
     * @param int                                     $supplierReserveId
     * @param int                                     $orderItemId
     * @param int|null                                $quantity
     *
     * @return int
     */
    public function itemSupplierReserveReset(\ServiceBundle\Services\RegisterService $service, int $supplierReserveId, int $orderItemId, $quantity = null) : int
    {
        $sql = '
            SELECT
                quantity AS current_quantity
            FROM
                supplier_reserve_item
            WHERE
                supplier_reserve_id = :supplier_reserve_id
                AND order_item_id = :order_item_id
        ';

        $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('supplier_reserve_id', $supplierReserveId);
        $query->setParameter('order_item_id', $orderItemId);

        $currentQuantity = $query->getSingleScalarResult();

        $quantity = $quantity != null && $quantity < $currentQuantity ? $quantity : $currentQuantity;

        $supplierReserveItem = $this->getEm()->getRepository(SupplierReserveItem::class)->findOneBy([
            'supplierReserveId' => $supplierReserveId,
            'orderItemId' => $orderItemId,
        ]);

        if (!$supplierReserveItem) {
            throw new NotFoundHttpException('SupplierReserveItem not found');
        }

        $supplierReserveItem->setQuantity($supplierReserveItem->getQuantity() - $quantity);
        $this->getEm()->persist($supplierReserveItem);
        $this->getEm()->flush();

        // Пересоздаем регистры
        $service->supplierReserveItem([$supplierReserveItem->getId(),]);

        return $quantity;
    }

    /**
     * @param \ServiceBundle\Services\RegisterService $serviceRegister
     * @param User  $currentUser
     * @param int   $supplierReserveId
     * @param array $orderItemList [[order_item_id => quantity]]
     * @param array $goodsRequestList [[goods_request_id => quantity]]
     * @param null  $parentDocId
     * @param null  $parentDocType
     */
    public function supplierGoodsReservation(\ServiceBundle\Services\RegisterService $serviceRegister, User $currentUser, int $supplierReserveId, array $orderItemList, array $goodsRequestList, $parentDocId = null, $parentDocType = null) : void
    {
        $sql = '
            INSERT INTO supplier_goods_reservation ( supplier_reserve_id, created_by, parent_doc_type, parent_doc_id )
            VALUES (:supplier_reserve_id::INTEGER, :user_id::INTEGER, :parent_doc_type, :parent_doc_id::INTEGER)
            RETURNING id
        ';

        $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('supplier_reserve_id', $supplierReserveId);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('parent_doc_type', $parentDocType);
        $query->setParameter('parent_doc_id', $parentDocId);

        $reservationId = $query->execute();
        $values = [];

        foreach ($orderItemList as $id => $quantity) {
            $values[] = sprintf('(%u::INTEGER, NULL, %u::INTEGER, %u::INTEGER, NULL)', $id, $quantity, $reservationId);
        }

        foreach ($goodsRequestList as $id => $quantity) {
            $values[] = sprintf('(NULL, %u::INTEGER, %u::INTEGER, %u::INTEGER, NULL)', $id, $quantity, $reservationId);
        }

        if ($reservationId) {
            $sql = '
                WITH DATA (order_item_id, goods_request_id, delta, supplier_goods_reservation_id, base_product_id) AS
                ( VALUES '.implode(',', $values).')
                INSERT INTO supplier_goods_reservation_item (order_item_id, goods_request_id, delta, supplier_goods_reservation_id, base_product_id)
                SELECT
                    d.order_item_id,
                    d.goods_request_id,
                    d.delta,
                    d.supplier_goods_reservation_id,
                    COALESCE(oi.base_product_id, gr.base_product_id)
                FROM
                    DATA AS d
                    LEFT JOIN order_item AS oi ON oi.id = d.order_item_id
                    LEFT JOIN goods_request AS gr ON gr.id = d.goods_request_id
            ';

            $query = $this->getEm()->createNativeQuery($sql, new ResultSetMapping());
            $query->execute();

            $serviceRegister->supplierGoodsReservation([$reservationId,]);
        }
    }

    /**
     * @param null   $state
     * @param null   $supplierId
     * @param null   $fromDate
     * @param null   $toDate
     * @param null   $supplyId
     *
     * @return mixed
     */
    public function getSupplierWithInvoices($state = null, $supplierId = null, $fromDate = null, $toDate = null, $supplyId = null)
    {
        if ($state === self::STATE_WAYBILL) {
            if (empty($fromDate) && empty($supplyId)) {
                throw new BadRequestHttpException('Не указана дата начала или номер документа');
            } else {
                if (!empty($fromDate)) {
                    $t = new \DateTime( $fromDate->format('Y-m-d') . ' 00:00:00');
                    $fromDate = $t->getTimestamp();

                    if (empty($toDate)) {
                        $toDate = new \DateTime();
                    }

                    $t = new \DateTime( $toDate->format('Y-m-d') . ' 23:59:59');
                    $toDate = $t->getTimestamp();

                    if ($fromDate > $toDate) {
                        throw new BadRequestHttpException('Дата начала не должна превышать дату окончания');
                    }
                }
            }
        }

        if ($state === self::STATE_WAYBILL) {
            $sql = !empty($supplyId) ? 'AND s.id = :supply_id' : '';
            $stateSql = (!empty($fromDate)) ? "AND ga.completed_at IS NOT NULL AND (ga.completed_at BETWEEN to_timestamp(:sinceDate) AND to_timestamp(:tillDate) {$sql})" : $sql;
        } elseif ($state === self::STATE_TRANSIT) {
            $stateSql = 'AND ga.completed_at IS NULL AND s.registered_at IS NOT NULL';
        } elseif ($state === self::STATE_FORMING) {
            $stateSql = 'AND s.registered_at IS NULL';
        } else {
            $stateSql = !empty($supplyId) ? 'AND s.id = :supply_id' : '';
        }

        $idSql = !empty($supplierId) ? 'AND s.supplier_id = :supplier_id ' : '';

        $q = $this->getEm()->createNativeQuery('
            SELECT
                s.id /*ид счета*/,
                s.created_at as date /*дата*/,
                gp.code AS point /*точка прибытия*/,
                SUM ( si.quantity * si.purchase_price )::INTEGER AS "sum" /*сумма*/,
                COUNT ( DISTINCT si.base_product_id )::INTEGER AS quantity /*количество наименований товаров*/,
                COALESCE ( ga.arriving_time, s.our_waybill_date ) AS arriving_time /*дата прихода*/,
                ga.number AS waybill_number /* номер накладной */,
                ga.completed_at AS waybill_date /* дата накладной */,
                s.supplier_counteragent_id /*контрагент поставщика*/,
                os.short_name AS our_counteragent /*наше юр.лицо, на котрое выставлен счет*/,
                s.supplier_invoice_number /*номер счета поставщика*/,
                s.comment /*комментарий */,
                vup.fullname AS creator /*кто создал*/,
                CASE 
                    WHEN s.registered_at IS NULL THEN :forming::TEXT  
                    WHEN ga.completed_at IS NULL THEN :transit::TEXT  
                    WHEN ga.completed_at IS NOT NULL THEN :waybill::TEXT
                    ELSE NULL
                END AS state
            FROM
                supply AS s
                LEFT JOIN goods_acceptance_doc AS ga ON ga.parent_doc_id = s.id 
                    AND ga.parent_doc_type = :supply
                JOIN geo_point AS gp ON gp.id = s.destination_point_id
                LEFT JOIN supply_item AS si ON si.parent_doc_type = :supply 
                    AND si.parent_doc_id = s.id 
                JOIN our_seller AS os ON os.counteragent_id = s.our_counteragent_id
                LEFT JOIN func_view_user_person ( s.created_by ) AS vup ON 1 = 1 
            WHERE
                1 = 1
                '.$idSql.' 
                '.$stateSql.' 
            GROUP BY
                s.id,
                gp.code,
                ga.did,
                os.short_name,
	            vup.fullname
        ', new DTORSM(\SupplyBundle\Bus\Data\Query\DTO\SupplierWithInvoices::class));

        $q->setParameter('supplier_id', $supplierId);
        $q->setParameter('supply_id', $supplyId);
        $q->setParameter('supply', DocumentTypeCode::SUPPLY);
        $q->setParameter('sinceDate', $fromDate, Type::INTEGER);
        $q->setParameter('tillDate', $toDate, Type::INTEGER);
        $q->setParameter('forming', self::STATE_FORMING, Type::STRING);
        $q->setParameter('transit', self::STATE_TRANSIT, Type::STRING);
        $q->setParameter('waybill', self::STATE_WAYBILL, Type::STRING);

        $rows = $q->getResult('DTOHydrator');

        foreach ($rows as &$row) {
            if (!empty($state) && in_array($state, [self::STATE_WAYBILL, self::STATE_TRANSIT, self::STATE_FORMING,])) {
                $row->state = $state;
            }
        }

        return $rows;
    }
}
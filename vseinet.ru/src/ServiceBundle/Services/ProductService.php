<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\ProductPriceType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PricingBundle\Entity\Product;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductService extends MessageHandler
{
    const CHUNK_SIZE = 10;

    const TYPE_INTEGER = 'integer';
    const TYPE_TIMESTAMP = 'timestamp';

    /**
     * Fields for update
     */
    const FIELDS = [
        'geo_city_id' => self::TYPE_INTEGER,
        'base_product_id' => self::TYPE_INTEGER,
        'product_availability_code' => 'product_availability_code',
        'price' => self::TYPE_INTEGER,
        'price_type' => 'product_price_type',
        'price_time' => self::TYPE_TIMESTAMP,
        'discount_amount' => self::TYPE_INTEGER,
        'offer_percent' => self::TYPE_INTEGER,
        'created_at' => self::TYPE_TIMESTAMP,
        'modified_at' => self::TYPE_TIMESTAMP,
        'delivery_tax' => self::TYPE_INTEGER,
        'rise_tax' => self::TYPE_INTEGER,
        'special_price' => self::TYPE_INTEGER,
        'special_price_operated_by' => self::TYPE_INTEGER,
        'special_price_operated_at' => self::TYPE_TIMESTAMP,
        'ultimate_price' => self::TYPE_INTEGER,
        'ultimate_price_operated_by' => self::TYPE_INTEGER,
        'ultimate_price_operated_at' => self::TYPE_TIMESTAMP,
        'competitor_price' => self::TYPE_INTEGER,
        'temporary_price' => self::TYPE_INTEGER,
        'temporary_price_operated_at' => self::TYPE_TIMESTAMP,
        'temporary_price_operated_by' => self::TYPE_INTEGER,
        'rating' => self::TYPE_INTEGER,
    ];

    /**
     * @var array<Product>
     */
    protected $products = [];

    /**
     * @var array
     */
    protected $updatedBaseProductIds = [];

    /**
     * @var array
     */
    protected $updateFields = [];

    /**
     * @var integer
     */
    protected $uploadedQuantity = 0;

    /**
     * @param array $baseProductIds
     *
     * @return array
     */
    public function update(array $baseProductIds) : array
    {
        if (empty($baseProductIds)) {
            throw new BadRequestHttpException('Список id пустой');
        }

        $idsParts = array_chunk($baseProductIds, self::CHUNK_SIZE);
        $geoCityIds = $this->_getActiveCities();

        foreach ($idsParts as $idsPart) {
            $products = $this->_getProducts($idsPart, $geoCityIds);
            $extraProducts = $this->_getExtraProducts($products, $baseProductIds, $geoCityIds);

            foreach ($products as $id => $product) {
                $isUpdate = false;
                $updateFields = [];

                if (isset($extraProducts[$id])) {
                    $extraProduct = $extraProducts[$id];

                    foreach (self::FIELDS as $name => $type) {
                        if (array_key_exists($name, $product) && array_key_exists($name, $extraProduct) && $product[$name] != $extraProduct[$name]) {
                            $product[$name] = $extraProduct[$name];

                            $isUpdate = true;
                            $updateFields[$name] = $name;
                        }
                    }
                }

                if ($isUpdate) {
                    $this->products[$id] = $product;

                    foreach ($updateFields as $updateField) {
                        $this->updateFields[$updateField] = $updateField;
                    }
                }
            }
        }

        $this->_flush();

        return $baseProductIds;
    }


    /**
     * @param array $baseProductIds
     * @param array $geoCityIds
     *
     * @return array
     */
    private function _getProducts(array $baseProductIds, array $geoCityIds) : array
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            SELECT
                p.* 
            FROM
                product p 
            WHERE
                p.base_product_id IN (:ids) 
                AND (p.geo_city_id IN (:city_ids) OR p.geo_city_id IS NULL)
            ORDER BY 
                p.base_product_id,
                p.id
        ', new ResultSetMapping());

        $query->setParameter('ids', $baseProductIds);
        $query->setParameter('city_ids', $geoCityIds);

        $rows = $query->getResult('ListAssocHydrator');

        $result = [];
        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    private function _getExtraProducts(array $products, array $baseProductIds, array $geoCityIds) : array
    {
        // product_availability_code
        $productDiff = $this->_getProductDiff($baseProductIds, $geoCityIds);

        foreach ($products as &$product) {
            foreach ($productDiff as $diff) {
                if ($product['base_product_id'] == $diff['base_product_id'] && $product['geo_city_id'] == $diff['geo_city_id']) {
                    $product['product_availability_code'] = $diff['product_availability_code'];
                    $product['price'] = $diff['price'];
                    $product['price_type'] = $diff['price_type'];
                }
            }
        }

        // другие параметры

        return $products;
    }

    /**
     * @param array $baseProductIds
     * @param array $geoCityIds
     *
     * @return array
     */
    private function _getProductDiff(array $baseProductIds, array $geoCityIds) : array
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $result = $values = [];
        foreach ($geoCityIds as $id) {
            $values[] = sprintf("(%s::INTEGER)", !empty($id) ? $id : 'NULL');
        }

        foreach ($baseProductIds as $baseProductId) {
            $query = $em->createNativeQuery('
                WITH competitor_product AS (
                  SELECT 
                    MIN ( pc.competitor_price ) AS competitor_price,
                    P.base_product_id,
                    P.geo_city_id 
                  FROM
                    product
                    AS P JOIN product_to_competitor AS pc ON pc.base_product_id = P.base_product_id
                    JOIN competitor AS C ON C.ID = pc.competitor_id 
                  WHERE
                    C.is_active = TRUE 
                    AND pc.competitor_price > 0 
                    AND P.base_product_id = :base_product_id 
                  AND
                  CASE WHEN C.channel IN ( :site, :pricelist ) THEN
                    pc.price_time + INTERVAL \'7 day\' >= now() ELSE pc.price_time + INTERVAL \'30 day\' >= now() 
                  END 
                    AND (
                      COALESCE ( pc.geo_city_id, P.geo_city_id ) = P.geo_city_id 
                      OR P.geo_city_id IS NULL 
                      AND pc.geo_city_id IS NULL 
                    ) 
                  GROUP BY
                    P.base_product_id,
                    P.geo_city_id 
                ),
                standard_product AS (
                  SELECT
                   bp.ID as base_product_id,
                   round(
                   bp.supplier_price * (
                   100 + (
                  SELECT
                   tm.margin_percent 
                  FROM
                   trade_margin AS tm
                   JOIN category_path cc ON cc.pid = tm.category_id 
                  WHERE
                   cc.ID = category_id 
                   AND bp.supplier_price BETWEEN lower_limit 
                   AND higher_limit 
                  ORDER BY
                   cc.plevel DESC 
                   LIMIT 1 
                   )) / 100, - 2 
                   ) AS retail_price,
                   product.geo_city_id
                  FROM
                   base_product bp 
                   INNER JOIN product ON bp.id = product.base_product_id
                 WHERE bp.id = :base_product_id
                ),
                 DATA AS ( SELECT * FROM get_goods_reserve_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP, :base_product_id ) ),
                CITIES ( geo_city_id ) ( VALUES '.implode(',', $values).') 
                
                SELECT
                  bp.ID AS base_product_id,
                  c.geo_city_id :: INTEGER AS geo_city_id,
                  CASE 
                      WHEN (
                      SELECT SUM
                      ( d.delta ) 
                      FROM
                          DATA AS d
                          JOIN geo_room AS r ON r.id = d.geo_room_id
                          JOIN geo_point AS P ON P.id = r.geo_point_id 
                      WHERE
                          d.goods_condition_code = :free 
                          AND d.order_item_id IS NULL 
                          AND P.geo_city_id = c.geo_city_id::INTEGER 
                          ) > 0 
                      THEN :available
                      WHEN (
                          SELECT SUM
                              ( d.delta ) 
                          FROM
                              DATA AS d
                              JOIN order_item AS oi ON oi.id = d.order_item_id
                              JOIN "order" AS o ON o.id = oi.order_id 
                          WHERE
                              d.goods_condition_code = :free 
                              AND o.geo_city_id = c.geo_city_id::INTEGER
                              ) > 0 
                      THEN :in_transit
                      WHEN (
                          SELECT SUM
                              ( d.delta ) 
                          FROM
                              DATA AS d
                              JOIN order_item AS oi ON oi.id = d.order_item_id
                              JOIN "order" AS o ON o.id = oi.order_id
                              JOIN representative AS rp ON rp.geo_point_id = o.geo_point_id 
                          WHERE
                              d.goods_condition_code = :free
                              AND rp.has_transit = TRUE 
                          ) > 0 
                          OR (
                          SELECT SUM
                              ( d.delta ) 
                          FROM
                              DATA AS d
                              JOIN geo_room AS r ON r.id = d.geo_room_id
                              JOIN representative AS rp ON rp.geo_point_id = r.geo_point_id 
                          WHERE
                              d.goods_condition_code = :free 
                              AND d.order_item_id IS NULL 
                              AND rp.has_transit = TRUE 
                          ) > 0 
                          OR bp.supplier_availability_code IN ( :on_demand, :available ) 
                      THEN :on_demand
                      WHEN bp.supplier_availability_code = :in_transit 
                      THEN :awaiting
                      ELSE :out_of_stock 
                  END  as product_availability_code,
                  CASE    
                    WHEN product.ultimate_price > 0 
                    AND product.product_availability_code = :available THEN
                      product.ultimate_price 
                      WHEN product.manual_price > 0 THEN
                      product.manual_price 
                      WHEN cp.competitor_price > 0 
                      AND sp.retail_price + product.delivery_tax > cp.competitor_price 
                      AND cp.competitor_price > bp.supplier_price THEN
                        cp.competitor_price * 0.3 + 0.7 * sp.retail_price - product.delivery_tax 
                        WHEN bp.price_retail_min > sp.retail_price THEN
                        bp.price_retail_min ELSE sp.retail_price 
                      END AS price,
                  CASE    
                    WHEN product.ultimate_price > 0 
                    AND product.product_availability_code = :available THEN :ultimate
                      WHEN product.manual_price > 0 THEN :manual
                      WHEN cp.competitor_price > 0 
                      AND sp.retail_price + product.delivery_tax > cp.competitor_price 
                      AND cp.competitor_price > bp.supplier_price THEN :compared
                        WHEN bp.price_retail_min > sp.retail_price THEN :recommended ELSE :standard 
                      END AS price_type       
                    FROM
                      base_product AS bp
                      INNER JOIN product ON bp.id = product.base_product_id
                      LEFT JOIN CITIES AS c ON c.geo_city_id = product.geo_city_id
                      LEFT JOIN competitor_product AS cp ON cp.base_product_id = product.base_product_id AND cp.geo_city_id = product.geo_city_id
                      LEFT JOIN standard_product AS sp ON sp.base_product_id = product.base_product_id AND sp.geo_city_id = product.geo_city_id
                    WHERE
                    bp.id = cp.base_product_id            
                    AND bp.id = :base_product_id 
            ', new ResultSetMapping());

            $query->setParameter('site', CompetitorTypeCode::SITE);
            $query->setParameter('pricelist', CompetitorTypeCode::PRICELIST);
            $query->setParameter('free', GoodsConditionCode::FREE);
            $query->setParameter('available', ProductAvailabilityCode::AVAILABLE);
            $query->setParameter('in_transit', ProductAvailabilityCode::IN_TRANSIT);
            $query->setParameter('on_demand', ProductAvailabilityCode::ON_DEMAND);
            $query->setParameter('awaiting', ProductAvailabilityCode::AWAITING);
            $query->setParameter('out_of_stock', ProductAvailabilityCode::OUT_OF_STOCK);
            $query->setParameter('ultimate', ProductPriceType::ULTIMATE);
            $query->setParameter('manual', ProductPriceType::MANUAL);
            $query->setParameter('compared', ProductPriceType::COMPARED);
            $query->setParameter('recommended', ProductPriceType::RECOMMENDED);
            $query->setParameter('standard', ProductPriceType::STANDARD);
            $query->setParameter('base_product_id', $baseProductId);

            $result = array_merge($result, $query->getResult('ListAssocHydrator'));
        }

        return $result;
    }

    /**
     * @return array
     */
    private function _getActiveCities() : array
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            SELECT 
                P.geo_city_id 
            FROM
                representative AS r
                JOIN geo_point AS P ON P.id = r.geo_point_id 
            WHERE
                r.is_active = TRUE 
                AND r.has_retail = TRUE 
            GROUP BY
                P.geo_city_id
            ORDER BY 
                P.geo_city_id
        ', new ResultSetMapping());

        $rows = $query->getResult('ListAssocHydrator');

        $result = [null,];
        foreach ($rows as $row) {
            $result[] = $row['geo_city_id'];
        }

        return $result;
    }

    /**
     * Flush products to db
     */
    private function _flush()
    {
        while (!empty($this->products)) {
            $this->_flushSupplierProducts();
        }
    }

    /**
     * Flush products
     */
    private function _flushSupplierProducts()
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $columns = implode(', ', array_merge(array_keys($this->updateFields), ['geo_city_id', 'base_product_id',]));
        $products = array_splice($this->products, 0, self::CHUNK_SIZE);

        $placeholders = [];
        /**
         * @var $product Product
         */
        foreach ($products as $product) {
            $phs = [];
            foreach ($this->updateFields as $column) {
                $type = self::FIELDS[$column];

                if (self::TYPE_TIMESTAMP === $type) {
                    $value = new \DateTime($product[$column]);
                    $value = $value->format('y-m-d H:i:s');

                    $phs[] = "to_timestamp('".$value."', 'YYYY-MM-DD HH24:MI:SS')";
                } else {
                    $value = $product[$column];

                    $phs[] = ((self::TYPE_INTEGER === $type) ? $value : "'" . $value . "'").'::'.$type;
                }
            }

            $phs[] = (empty($product['geo_city_id']) ? 'NULL' : $product['geo_city_id']).'::'.self::FIELDS['geo_city_id'];
            $phs[] = $product['base_product_id'].'::'.self::FIELDS['base_product_id'];

            $placeholders[] = '('.implode(', ', $phs).')';

            $this->updatedBaseProductIds[] = $product['base_product_id'];
        }

        $placeholders = implode(',', $placeholders);

        $fields = [];
        foreach ($this->updateFields as $column) {
            $fields[] = $column.' = data.'.$column;
        }

        $sql = "
            WITH data ({$columns}) AS (
                VALUES {$placeholders}
            )
            UPDATE product 
            SET ".implode(', ', $fields)."
            FROM data 
            WHERE COALESCE(product.geo_city_id, 0) = COALESCE(data.geo_city_id, 0) AND product.base_product_id = data.base_product_id
        ";

        $stmt = $em->getConnection()->prepare($sql);
        if ($stmt->execute()) {
            echo 'Products updated'.PHP_EOL;
        } else {
            echo 'Error products updated'.PHP_EOL;
            print_r($stmt->errorInfo());
        }

        $this->uploadedQuantity += count($products);
    }
}
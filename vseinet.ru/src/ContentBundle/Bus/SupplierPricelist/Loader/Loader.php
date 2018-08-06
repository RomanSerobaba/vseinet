<?php 

namespace ContentBundle\Bus\SupplierPricelist\Loader;

use AppBundle\Container\ContainerAware;
use PricingBundle\Entity\Currency;
use AppBundle\Enum\ProductAvailabilityCode;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierProduct;
use SupplyBundle\Entity\SupplierProductBarCode;
use ContentBundle\Entity\ParserSource;
use ContentBundle\Entity\ParserProduct;
use ContentBundle\Entity\ParserImage;
use PricingBundle\Entity\Competitor;
use PricingBundle\Entity\ProductToCompetitor;

class Loader extends ContainerAware
{
    /**
     * Maximum number of parameters
     */
    const MAX_NUMBER_PARAMETERS = 65535;

    /**
     * Fields for update
     */
    const FIELDS = [
        'supplier_id' => 'integer',
        'supplier_category_id' => 'integer',
        'name' => 'text',
        'name_hash' => 'text',
        'brand_id' => 'integer',
        'model' => 'text',
        'code' => 'text',
        'article' => 'text',
        'description' => 'text',
        'price' => 'integer',
        'price_retail_min' => 'integer',
        'competitor_price' => 'integer',
        'original_currency' => 'text',
        'original_price' => 'integer',
        'product_availability_code' => 'product_availability_code',
        'min_quantity' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * @var Supplier
     */
    protected $supplier;

    /**
     * @var bool
     */
    protected $isKeepCategories;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @deprecated
     * @var array<SupplierCategory>
     */
    protected $categories;

    /**
     * @var array<integer>
     */
    protected $categoryIds;

    /**
     * @deprecated
     * @var array<Brand>
     */
    protected $brands;

    /**
     * @var array<integer>
     */
    protected $brandIds;

    /**
     * @var array<SupplierProduct>
     */
    protected $supplierProducts;

    /**
     * @var array<SupplierProductBarCode>
     */
    protected $productBarCodes;

    /**
     * @var array<ParserSource>
     */
    protected $parserSources;

    /**
     * @var array<ParserProduct>
     */
    protected $parserProducts;

    /**
     * @var array<ParserImage>
     */
    protected $parserImages;

    /**
     * @var Competitor
     */
    protected $competitor;

    /**
     * @var array<ProductToCompetitor>
     */
    protected $productToCompetitor;

    /**
     * @var integer
     */
    protected $uploadedQuantity;


    /**
     * @param Supplier $supplier 
     * 
     * @return Loader
     */
    public function init(Supplier $supplier, bool $isKeepCategories)
    {
        $this->supplier = $supplier;
        $this->isKeepCategories = $isKeepCategories;

        $this->em = $this->getDoctrine()->getManager();
        $this->currency = $this->em->getRepository(Currency::class)->findOneBy([], ['date' => 'DESC']);
        
        $this->uploadedQuantity = 0;
         
        return $this;
    }

    /**
     * Get maximum number of products for load
     * 
     * @return integer
     */
    public function getMaxNumberProductsForLoad()
    {
        return intdiv(self::MAX_NUMBER_PARAMETERS, count(self::FIELDS));
    }

    /**
     * Get uploadedQuantity
     * 
     * @return integer
     */
    public function getUploadedQuantity()
    {
        return $this->uploadedQuantity;
    }

    /**
     * Prepare data
     * 
     * @param array $data
     */
    public function prepare(array $data)
    {
        $data = new Data($data, $this->currency);
        if (ProductAvailabilityCode::OUT_OF_STOCK == $data->getAvailability() && !$data->getPrice()) {
            return null;
        }

        if ($data->getBarCodes()) {
            $supplierProduct = $this->findProductByBarCodes($data->getBarCodes());
        }
        if (empty($supplierProduct) || !$supplierProduct instanceof SupplierProduct) {
            $supplierProduct = new SupplierProduct();
            $supplierProduct->setSupplierId($this->supplier->getId());
            $supplierProduct->setCreatedAt(new \DateTime());
        }
        
        if (null === $supplierProduct->getCategoryId() || !$this->isKeepCategories) {
            $supplierProduct->setCategoryId($this->getCategoryId($data->getCategories()));
        }

        if ($data->getBrand()) {
            $supplierProduct->setBrandId($this->getBrandId($data->getBrand()));
        }
        else {
            $supplierProduct->setBrandId($this->getBrandIdByProductName($data->getName()));
        }

        $supplierProduct->setName($data->getName());
        $supplierProduct->setNameHash($data->getNameHash());
        $supplierProduct->setModel($data->getModel());
        $supplierProduct->setDescription($data->getDescription());
        $supplierProduct->setCode($data->getCode());
        $supplierProduct->setArticle($data->getArticle());
        $supplierProduct->setPrice($data->getPrice());
        $supplierProduct->setPriceRetailMin($data->getPriceRetailMin());
        $supplierProduct->setCompetitorPrice($data->getCompetitorPrice());
        $supplierProduct->setOriginalCurrency($data->getOriginalCurrency());
        $supplierProduct->setOriginalPrice($data->getOriginalPrice());
        $supplierProduct->setAvailabilityCode($data->getAvailability());
        $supplierProduct->setMinQuantity($data->getMinQuantity());
        $supplierProduct->setUpdatedAt(new \DateTime()); 

        // bar codes
        if ($data->getBarCodes()) {
            foreach ($data->getBarCodes() as $barCode) {
                $productBarCode = new SupplierProductBarCode();
                $productBarCode->setBarCode($barCode);
                $this->productBarCodes[$data->getNameHash()][] = $productBarCode;
            }
        }

        // parser
        if ($data->getUrl()) {
            if (empty($this->parserSources)) {
                $this->parserSources = $this->em->getRepository(ParserSource::class)->findBy([
                    'supplierId' => $this->supplier->getId(),
                ]);
            }
            foreach ($this->parserSources as $parserSource) {
                if (false !== stripos($data->getUrl(), $parserSource->getUrl())) {
                    $parserProduct = new ParserProduct();
                    $parserProduct->setSourceId($parserSource->getId());
                    $parserProduct->setUrl($data->getUrl());
                    $this->parserProducts[$data->getNameHash()] = $parserProduct;
                    break;
                }
            }
        }
        if ($data->getUrlImages()) {
            if (empty($this->parserSources)) {
                $this->parserSources = $this->em->getRepository(ParserSource::class)->findBy([
                    'supplierId' => $this->supplier->getId(),
                ]);
            } 
            foreach ($this->parserSources as $parserSource) {
                if (false !== stripos($data->getUrlImages()[0], $parserSource->getUrl())) {
                    foreach ($data->getUrlImages() as $urlImage) {
                        $parserImage = new ParserImage();
                        $parserImage->setSourceId($parserSource->getId());
                        $parserImage->setUrl($urlImage);
                        $this->parserImages[$data->getNameHash()][] = $parserImage;
                    }
                    break;
                }
            }           
        }

        // product to competitor
        if ($data->getCompetitorPrice() && false !== $this->competitor) {
            if (null === $this->competitor) {
                $this->competitor = $this->em->getRepository(Competitor::class)->findOneBy([
                    'supplierId' => $this->supplier->getId(),
                ]);
            }
            if ($this->competitor instanceof Competitor) {
                $productToCompetitor = new ProductToCompetitor();
                $productToCompetitor->setCompetitorId($this->competitor->getId());
                $productToCompetitor->setCompetitorPrice($data->getCompetitorPrice());
                if ($data->getUrl()) {
                    $productToCompetitor->setLink($data->getUrl());    
                }
                $productToCompetitor->setPriceTime(new \DateTime());
                $this->competitorToProducts[$data->getNameHash()] = $productToCompetitor;   
            }
            else {
                $this->competitor = false;
            }
        }

        return $this->supplierProducts[$data->getNameHash()] = $supplierProduct;
    }

    /**
     * Flush products to db
     */
    public function flush()
    {
        while (!empty($this->supplierProducts)) {
            $supplierProducts = $this->flushSupplierProducts();
            if (!empty($this->productBarCodes)) {
                $this->flushBarCodes($supplierProducts);
            }
            if (!empty($this->parserProducts)) {
                $this->flushParserProducts($supplierProducts);
            }
            if (!empty($this->parserImages)) {
                $this->flushParserImages($supplierProducts);
            }
            if (!empty($this->competitorToProducts)) {
                $this->flushCompetitorToProducts($supplierProducts);
            }
        }
    }

    /**
     * Flush supplier products
     * 
     * @return array<SupplierProduct>
     */
    protected function flushSupplierProducts()
    {
        $columns = implode(', ', array_keys(self::FIELDS));

        $supplierProducts = array_splice($this->supplierProducts, 0, $this->getMaxNumberProductsForLoad());

        $placeholders = [];
        foreach (self::FIELDS as $column => $type) {
            if ('created_at' == $column || 'updated_at' == $column) {
                $placeholders[] = "to_timestamp(?, 'YYYY-MM-DD HH24:MI:SS')";
                continue;
            }
            $placeholders[] = "?::{$type}";
        }
        $placeholders = implode(',', array_fill(0, count($supplierProducts), '('.implode(', ', $placeholders).')'));

        $parameters = [];
        foreach ($supplierProducts as $supplierProduct) {
            $parameters[] = $supplierProduct->getSupplierId();
            $parameters[] = $supplierProduct->getCategoryId();
            $parameters[] = $supplierProduct->getName(); 
            $parameters[] = $supplierProduct->getNameHash();
            $parameters[] = $supplierProduct->getBrandId();
            $parameters[] = $supplierProduct->getModel();
            $parameters[] = $supplierProduct->getCode(); 
            $parameters[] = $supplierProduct->getArticle();
            $parameters[] = $supplierProduct->getDescription();
            $parameters[] = $supplierProduct->getPrice();
            $parameters[] = $supplierProduct->getPriceRetailMin();
            $parameters[] = $supplierProduct->getCompetitorPrice();
            $parameters[] = $supplierProduct->getOriginalCurrency();
            $parameters[] = $supplierProduct->getOriginalPrice();
            $parameters[] = $supplierProduct->getAvailabilityCode();
            $parameters[] = $supplierProduct->getMinQuantity();
            $parameters[] = $supplierProduct->getCreatedAt()->format('y-m-d H:i:s');
            $parameters[] = $supplierProduct->getUpdatedAt()->format('y-m-d H:i:s');  
        }

        $stmt = $this->em->getConnection()->prepare("
            WITH 
                data ({$columns}) AS (
                    VALUES {$placeholders}
                ),
                updated AS (
                    UPDATE supplier_product 
                    SET 
                        supplier_category_id = data.supplier_category_id,
                        brand_id = data.brand_id,
                        model = data.model,
                        code = data.code,
                        article = data.article,
                        description = data.description,
                        price = data.price,
                        price_retail_min = data.price_retail_min,
                        competitor_price = data.competitor_price,
                        original_currency = data.original_currency,
                        original_price = data.original_price,
                        product_availability_code = data.product_availability_code,
                        min_quantity = data.min_quantity,
                        updated_at = data.updated_at
                    FROM data 
                    WHERE supplier_product.supplier_id = data.supplier_id 
                        AND supplier_product.name_hash = data.name_hash
                    RETURNING *
                )
            INSERT INTO supplier_product ({$columns})
            SELECT {$columns}
            FROM data 
            WHERE NOT EXISTS (SELECT 1 FROM updated)
        ");
        $stmt->execute($parameters);
        
        $this->uploadedQuantity += count($supplierProducts);

        return $supplierProducts;
    }

    /**
     * Flush bar codes
     * 
     * @param array<SupplierProduct> $supplierProducts
     */
    protected function flushBarCodes(array $supplierProducts)
    {
        $parameters = [];
        foreach ($supplierProducts as $hash => $supplierProduct) {
            if (empty($this->productBarCodes[$hash])) {
                continue;
            }
            foreach ($this->productBarCodes[$hash] as $productBarCode) {
                $parameters[] = $productBarCode->getBarCode();
                $parameters[] = $hash;
            }
            unset($this->productBarCodes[$hash]);
        }

        if (empty($parameters)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($parameters) / 2, '(?::text, ?::text)'));

        $parameters[] = $this->supplier->getId();

        $stmt = $this->em->getConnection()->prepare("
            WITH 
                data (bar_code, supplier_product_name_hash) AS (
                    VALUES {$placeholders}
                )
            INSERT INTO supplier_product_bar_code (bar_code, supplier_product_id)
            SELECT 
                data.bar_code,
                sp.id 
            FROM data 
            INNER JOIN supplier_product sp ON sp.name_hash = data.supplier_product_name_hash
            WHERE sp.supplier_id = ?
            ON CONFLICT
            DO NOTHING  
        ");
        $stmt->execute($parameters);
    }

    /**
     * Flush parser products
     * 
     * @param array<SupplierProduct> $supplierProducts
     */
    protected function flushParserProducts(array $supplierProducts) 
    {
        $parameters = [];
        foreach ($supplierProducts as $hash => $supplierProduct) {
            if (empty($this->parserProducts[$hash])) {
                continue;
            }
            $parameters[] = $this->parserProducts[$hash]->getSourceId();
            $parameters[] = $this->parserProducts[$hash]->getUrl();
            $parameters[] = $hash;
            unset($this->parserProducts[$hash]);
        }
        if (empty($parameters)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($parameters) / 3, '(?::integer, ?::text, ?::text)'));

        $parameters[] = $this->supplier->getId();

        $stmt = $this->em->getConnection()->prepare("
            WITH
                data (parser_source_id, url, supplier_product_name_hash) AS (
                    VALUES {$placeholders}
                )
            INSERT INTO parser_product (parser_source_id, supplier_product_id, base_product_id, url)
            SELECT 
                data.parser_source_id,
                sp.id,
                sp.base_product_id,
                data.url 
            FROM data 
            INNER JOIN supplier_product sp ON sp.name_hash = data.supplier_product_name_hash
            WHERE sp.supplier_id = ?
            ON CONFLICT
            DO NOTHING
        ");
        $stmt->execute($parameters);
    }

    /**
     * Flush parser products
     * 
     * @param array<SupplierProduct> $supplierProducts
     */
    protected function flushParserImages(array $supplierProducts) 
    {
        $parameters = [];
        foreach ($supplierProducts as $hash => $supplierProduct) {
            if (empty($this->parserImages[$hash])) {
                continue;
            }
            foreach ($this->parserImages[$hash] as $parserImage) {
                $parameters[] = $parserImage->getSourceId();
                $parameters[] = $parserImage->getUrl();
                $parameters[] = $hash;
            }
            unset($this->parserImages[$hash]);
        }
        if (empty($parameters)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($parameters) / 3, '(?::integer, ?::text, ?::text)'));

        $parameters[] = $this->supplier->getId();

        $stmt = $this->em->getConnection()->prepare("
            WITH 
                data (parser_source_id, url, supplier_product_name_hash) AS (
                    VALUES {$placeholders}
                )
            INSERT INTO parser_image (parser_source_id, supplier_product_id, base_product_id, url)
            SELECT 
                data.parser_source_id,
                sp.id,
                sp.base_product_id,
                data.url 
            FROM data 
            INNER JOIN supplier_product sp ON sp.name_hash = data.supplier_product_name_hash
            WHERE sp.supplier_id = ?
            ON CONFLICT
            DO NOTHING
        ");
        $stmt->execute($parameters);
    }

    /**
     * Flush product to competitors
     * 
     * @param array<SupplierProduct> $supplierProducts
     */
    protected function flushCompetitorToProducts(array $supplierProducts)
    {
        $parameters = [];
        foreach ($supplierProducts as $hash => $supplierProduct) {
            if (empty($this->productToCompetitors[$hash])) {
                continue;
            }
            $parameters[] = $this->productToCompetitors[$hash]->getCompetitorId();
            $parameters[] = $this->productToCompetitors[$hash]->getLink();
            $parameters[] = $this->productToCompetitors[$hash]->getCompetitorPrice();
            $parameters[] = $hash;
            unset($this->productToCompetitors[$hash]);
        }
        if (empty($parameters)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($parameters) / 4, '(?::integer, ?::text, ?::integer, ?::text)'));

        $parameters[] = $this->supplier->getId(); // CTE updated
        $parameters[] = $this->getParameter('default.city.id'); // CTE updated
        $parameters[] = $this->getParameter('default.city.id'); // main
        $parameters[] = $this->supplier->getId(); // main  

        $stmt = $this->em->getConnection()->prepare("
            WITH 
                data (competitor_id, link, competitor_price, supplier_product_name_hash) AS (
                    VALUES {$placeholders}
                ),
                updated AS (
                    UPDATE product_to_competitor
                    SET 
                        competitor_price = data.competitor_price
                        price_date = NOW()
                    FROM data 
                    INNER JOIN supplier_product sp ON sp.name_hash = data.supplier_product_name_hash
                    WHERE sp.supplier_id = ? 
                        AND product_to_competitor.base_product_id = sp.base_product_id
                        AND product_to_competitor.competitor_id = data.competitor_id
                        AND product_to_competitor.geo_city_id = ? 
                    RETURNING *
                )
            INSERT INTO product_to_competitor (
                competitor_id, 
                base_product_id, 
                geo_city_id, 
                link,
                competitor_price,
                price_time,
                status 
            ) 
            SELECT
                data.competitor_id,
                sp.base_product_id,
                ?,
                data.link,
                data.competitor_price,
                NOW(),
                'added'
            FROM data 
            INNER JOIN supplier_product so ON sp.name_hash = data.supplier_product_name_hash
            WHERE sp.supplier_id = ? 
                AND sp.base_product_id IS NOT NULL 
                AND NOT EXISTS (SELECT 1 FROM updated)
        ");
        $stmt->execute($parameters);
    }

    /**
     * @param array $barCodes 
     * 
     * @return SupplierProduct           
     */
    protected function findProductByBarCodes(array $barCodes) 
    {
        $q = $this->em->createQuery("
            SELECT sp 
            FROM ContentBundle:SupplierProduct sp 
            INNER JOIN SupplyBundle:SupplierProductBarCode spbc WITH spbc.productId = sp.id 
            WHERE sp.supplierId = :supplierId AND spbc.barCode IN (:barCodes)
        ");
        $q->setParameter('supplierId', $this->supplier->getId());
        $q->setParameter('barCodes', $barCodes);
        $q->setMaxResults(1);

       return $q->getResult();
    }

    /**
     * @param array<string> $names 
     * 
     * @return integer        
     */
    protected function getCategoryId(array $names)
    {
        $id = null;
        foreach ($names as $name) {
            $hash = md5($name.'-'.$id);
            if (!isset($this->categoryIds[$hash])) {
                $q = $this->em->getConnection()->prepare("
                    SELECT id  
                    FROM supplier_category 
                    WHERE name = :name AND pid = :pid 
                ");
                $q->execute(['name' => $name, 'pid' => $id]);
                if (false === ($this->categoryIds[$hash] = $q->fetchColumn())) {
                    $q = $this->em->getConnection()->prepare("
                        INSERT INTO supplier_category (name, pid, supplier_id)
                        VALUES (:name, :pid, :supplier_id)
                        RETURNING id 
                    ");
                    $q->execute(['name' => $name, 'pid' => $id, 'supplier_id' => $this->supplier->getId()]);
                    $this->categoryIds[$hash] = $q->fetchColumn();
                }
            }
            $id = $this->categoryIds[$hash];
        }

        return $id;
    }

    /**
     * @param string $name 
     * 
     * @return integer|null       
     */
    protected function getBrandId($name)
    {
        if (empty($name)) {
            return null;
        }

        $hash = md5($name);
        if (empty($this->brandIds[$hash])) {
            $q = $this->em->getConnection()->prepare("
                SELECT 
                    COALESCE(b.id, bp.brand_id) AS id,
                    CASE WHEN LOWER(b.name) = LOWER(?) THEN 1 WHEN LOWER(bp.name) = LOWER(?) THEN 2 END AS ORD
                FROM brand AS b 
                LEFT OUTER JOIN brand_pseudo bp ON bp.brand_id = b.id      
                WHERE LOWER(b.name) = LOWER(?) OR LOWER(bp.name) = LOWER(?)
                ORDER BY ORD 
                LIMIT 1
            ");
            $q->execute(array_fill(0, 4, $name));
            if (null === ($this->brandIds[$hash] = $q->fetchColumn())) {
                $q = $this->em->getConnection()->prepare("
                    INSERT INTO brand (name)
                    VALUES (:name)
                    RETURNING id 
                ");
                $q->execute(['name' => $name]);
                $this->brandIds[$hash] = $q->fetchColumn();
            }
        } 

        return $this->brandIds[$hash];
    }

    /**
     * @param string $name
     * 
     * @return integer|null
     */
    protected function getBrandIdByProductName($name)
    {
        $names[] = $name = preg_replace(['/[^\w+]/ui', '/\s+/'], ' ', mb_strtolower($name, 'UTF-8'));

        $parts = array_values(array_filter(explode(' ', $name), function($part) {
            return mb_strlen($part, 'UTF-8') > 1;
        }));

        $count = count($parts) - 1;
        if (0 < $count) {
            for ($index = 0; $index < $count; $index++) {
                $names[] = $parts[$index].' '.$parts[$index + 1];
            }
            $names = array_merge($names, $parts);
        }

        foreach ($names as $name) {
            $hash = md5($name);
            if (!empty($this->brandIds[$hash])) {
                return $this->brandIds[$hash];
            }
        }

        $parameters = [];
        $ord = 'CASE';
        foreach ($names as $index => $name) {
            $parameters[] = $name; 
            $ord .= ' WHEN LOWER(b.name) = ? THEN '.($index * 2);
            $ord .= ' WHEN LOWER(bp.name) = ? THEN '.($index * 2 + 1);
        }
        $ord .= ' END';
        $placeholders = str_repeat('?,', count($parameters) - 1).'?';

        $q = $this->em->getConnection()->prepare("
            SELECT 
                COALESCE(b.id, bp.brand_id) AS id,
                {$ord} AS ORD
            FROM brand b 
            LEFT OUTER JOIN brand_pseudo bp ON bp.brand_id = b.id 
            WHERE (LOWER(b.name) IN ({$placeholders}) OR LOWER(bp.name) IN ({$placeholders})) AND b.is_forbidden = false
            ORDER BY ORD
        ");
        $q->execute(array_merge($parameters, $parameters, $parameters, $parameters));
        $results = $q->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($results)) {
            return null;
        }

        $brandId = null;
        foreach ($results as $index => $result) {
            if (0 == $index) {
                $brandId = $result['id'];
            }
            $name = $names[intdiv($result['ord'], 2)];
            $this->brandIds[md5($name)] = $result['id'];
        }

        return $brandId;
    }
}
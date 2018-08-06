<?php

namespace ContentBundle\Bus\SupplierPricelist\Loader;

use PricingBundle\Entity\Currency;
use AppBundle\Enum\ProductAvailabilityCode;

class Data
{
    /**
     * @var string[]
     */
    private $barCodes;

    /**
     * @var string[]
     * 
     * @required
     */
    private $categories;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var string
     */
    private $brand;

    /**
     * @var int
     */
    private $brandId;

    /**
     * @var string
     * 
     * @required
     */
    private $name;

    /**
     * @var string
     */
    private $nameHash;

    /**
     * @var string 
     */
    private $model;

    /**
     * @var string 
     */
    private $description;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $article;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string[]
     */
    private $urlImages;

    /**
     * @var int
     * 
     * @required
     */
    private $price;

    /**
     * @var int
     */
    private $priceRetailMin;

    /**
     * @var int
     */
    private $competitorPrice;

    /**
     * @var string
     */
    private $originalCurrency;

    /**
     * @var int
     */
    private $originalPrice;

    /**
     * @var string
     * 
     * @required
     */
    private $availability;

    /**
     * @var int
     */
    private $minQuantity;

    
    public function __construct(array $data, Currency $currency)
    {
        if (!empty($data['bar_codes']) && is_array($data['bar_codes'])) {
            $this->barCodes = $this->cleanArrayStr($data['bar_codes']);
        }

        if (empty($data['categories']) || !is_array($data['categories'])) {
            throw new \RuntimeException(sprintf('Отсутствуют категории товара. %s', print_r($data, true)));
        }
        $this->categories = $this->cleanArrayStr($data['categories']);
        if (empty($this->categories)) {
            throw new \RuntimeException(sprintf('Отсутствуют категории товара. %s', print_r($data, true)));
        }

        if (!empty($data['brand'])) {
            $this->brand = $this->cleanStr($data['brand']);
        }

        if (empty($data['name'])) {
            throw new \RuntimeException(sprintf('Отсутствует наименование товара. %s', print_r($data, true)));
        }
        $this->name = $this->cleanStr($data['name']);
        $this->nameHash = md5($this->name);

        if (!empty($data['model'])) {
            $this->model = $this->cleanStr($data['model']);
        }

        if (!empty($data['description'])) {
            $this->description = $this->cleanStr($data['description']);
        }

        if (!empty($data['code'])) {
            $this->code = $this->cleanStr($data['code']);
        }

        if (!empty($data['article'])) {
            $this->article = $this->cleanStr($data['article']);
        }

        if (!empty($data['url'])) {
            $this->url = $this->cleanStr($data['url']);
        }

        if (!empty($data['url_images']) && is_array($data['url_images'])) {
            $this->urlImages = $this->cleanArrayStr($data['url_images']);
        }

        if (!array_key_exists('price', $data)) {
            throw new \RuntimeException(sprintf('Отсутствует цена товара. %s', print_r($data, true)));   
        }
        $this->price = $this->cleanPrice($data['price']);
        if (!empty($data['currency_price'])) {
            $this->originalCurrency = strtoupper($this->cleanStr($data['currency_price']));
            $this->originalPrice = $this->price;
            $this->price = $currency->convert($this->originalPrice, $this->originalCurrency);
        }
        if (!empty($data['coefficient_price'])) {
            $coefficient = filter_var($data['coefficient_price'], FILTER_VALIDATE_FLOAT);
            if (false === $coefficient) {
                throw new \RuntimeException(sprintf('Коэффициент цены должен быть числом. %s', print_r($data, true)));     
            }
            $this->price = round($this->price * $coefficient);
            if ($this->originalPrice) {
                $this->originalPrice = round($this->originalPrice * $coefficient);
            }
        }
        if (($this->price & 0x7FFFFFFF) != $this->price) {
            throw new \RuntimeException(sprintf('Цена товара должна быть от 0 до 2^31, %s', print_r($data, true)));
        }

        if (!empty($data['price_retail_min'])) {
            $this->priceRetailMin = $this->cleanPrice($data['price_retail_min']);
            if (!empty($data['currency_price_retail_min'])) {
                $this->priceRetailMin = $currency->convert($this->priceRetailMin, $this->cleanStr($data['currency_price_retail_min']));
            }
            if (!empty($data['coefficient_price_retail_min'])) {
                $coefficient = filter_var($data['coefficient_price_retail_min'], FILTER_VALIDATE_FLOAT);
                if (false === $coefficient) {
                    throw new \RuntimeException(sprintf('Коэффициент МРЦ должен быть числом. %s', print_r($data, true)));     
                } 
                $this->priceRetailMin = round($this->priceRetailMin * $coefficient);   
            }
        }
        if (($this->priceRetailMin & 0x7FFFFFFF) != $this->priceRetailMin) {
            throw new \RuntimeException(sprintf('МРЦ товара должна быть от 0 до 2^31, %s', print_r($data, true)));
        }

        if (array_key_exists('competitor_price', $data)) {
            $this->competitorPrice = $this->cleanPrice($data['competitor_price']);
            if (!empty($data['currency_competitor_price'])) {
                $this->competitorPrice = $currency->convert($this->competitorPrice, $this->cleanStr($data['currency_competitor_price']));
            }
            if (!empty($data['coefficient_competitor_price'])) {
                $coefficient = filter_var($data['coefficient_competitor_price'], FILTER_VALIDATE_FLOAT);
                if (false === $coefficient) {
                    throw new \RuntimeException(sprintf('Коэффициент цены конкурента должен быть числом. %s', print_r($data, true)));     
                } 
                $this->competitorPrice = round($this->competitorPrice * $coefficient);   
            }
        }
        if (($this->competitorPrice & 0x7FFFFFFF) != $this->competitorPrice) {
            throw new \RuntimeException(sprintf('Цена товара конкурента должна быть от 0 до 2^31, %s', print_r($data, true)));
        }

        if (empty($data['availability'])) {
            throw new \RuntimeException(sprintf('Отсутствует информация о наличии. %s', print_r($data, true)));    
        }
        $availability = [
            ProductAvailabilityCode::OUT_OF_STOCK,
            ProductAvailabilityCode::ON_DEMAND,
            ProductAvailabilityCode::IN_TRANSIT,
            ProductAvailabilityCode::AVAILABLE,
        ];
        if (!in_array($data['availability'], $availability)) {
            throw new \RuntimeException(sprintf('Неверная информация о наличии. Должно быть одно из %s. %s', implode(', ', $availability), print_r($data, true)));
        }
        $this->availability = $data['availability'];

        if (!empty($data['min_quantity'])) {
            $this->minQuantity = filter_var($data['min_quantity'], FILTER_VALIDATE_INT);
        }
        if (empty($this->minQuantity)) {
            $this->minQuantity = 1;
        }
        if (($this->minQuantity & 0x7FFFFFFF) != $this->minQuantity) {
            throw new \RuntimeException(sprintf('Минимальное количество должно быть от 0 до 2^31, %s', print_r($data, true)));
        }
    }

    protected function cleanStr($str) 
    {
        return trim(preg_replace('/\s+/', ' ', (string) $str));
    }

    protected function cleanArrayStr($arrayStr)
    {
        return array_filter(array_map([$this, 'cleanStr'], $arrayStr));
    }

    protected function cleanPrice($price)
    {
        return round(100 * floatval(str_replace([' ', ','], ['', '.'], $price)));
    }

    /**
     * Set categoryId
     * 
     * @param int $categoryId
     * 
     * @return Data
     */
    public function setCategoryId(int $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Get categoryId
     * 
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set brandId
     * 
     * @param int|null $brandId
     * 
     * @return Data
     */
    public function setBrandId(int $brandId = null)
    {
        $this->brandId = $brandId;
    }

    /**
     * Get brandId
     * 
     * @return int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Get barCodes
     * 
     * @return string[]
     */
    public function getBarCodes()
    {
        return $this->barCodes;
    }

    /**
     * Get categories
     * 
     * @return string[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get brand
     * 
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get nameHash
     * 
     * @return string
     */
    public function getNameHash()
    {
        return $this->nameHash;
    }

    /**
     * Get model
     * 
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Get code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get article
     * 
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Get url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get urlImages
     * 
     * @return string[]
     */
    public function getUrlImages()
    {
        return $this->urlImages;
    }

    /**
     * Get price
     * 
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get priceRetailMin
     * 
     * @return int
     */
    public function getPriceRetailMin() 
    {
        return $this->priceRetailMin;
    }

    /**
     * Get competitorPrice
     * 
     * @var int
     */
    public function getCompetitorPrice() 
    {
        return $this->competitorPrice;
    }

    /**
     * Get originalCurrency
     * 
     * @var string
     */
    public function getOriginalCurrency() 
    {
        return $this->originalCurrency;
    }

    /**
     * Get originalPrice
     * 
     * @var int
     */
    public function getOriginalPrice() 
    {
        return $this->originalPrice;
    }

    /**
     * Get availability
     * 
     * @var string
     */
    public function getAvailability() 
    {
        return $this->availability;
    }

    /**
     * Get minQuantity
     * 
     * @var int
     */
    public function getMinQuantity() 
    {
        return $this->minQuantity;
    }
}
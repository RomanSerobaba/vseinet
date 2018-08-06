<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierProduct
 *
 * @ORM\Table(name="supplier_product")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierProductRepository")
 */
class SupplierProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name_hash", type="string")
     */
    private $nameHash;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="article", type="string")
     */
    private $article;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="brand_id", type="integer")
     */
    private $brandId;

    /**
     * @var string
     *
     * @ORM\Column(name="product_availability_code", type="string")
     */
    private $availabilityCode;

    /**
     * @var int
     * 
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var string
     * 
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var bool
     * 
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;

    /**
     * @var string
     * 
     * @ORM\Column(name="model", type="string")
     */
    private $model;

    /**
     * @var int
     * 
     * @ORM\Column(name="price_retail_min", type="integer")
     */
    private $priceRetailMin;

    /**
     * @var int
     * 
     * @ORM\Column(name="competitor_price", type="integer")
     */
    private $competitorPrice;

    /**
     * @var int
     * 
     * @ORM\Column(name="min_quantity", type="integer")
     */
    private $minQuantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var int
     * 
     * @ORM\Column(name="original_price", type="integer")
     */
    private $originalPrice;

    /**
     * @var string
     * 
     * @ORM\Column(name="original_currency", type="string")
     */
    private $originalCurrency;
    

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SupplierProduct
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set nameHash
     *
     * @param string $nameHash
     *
     * @return SupplierProduct
     */
    public function setNameHash($nameHash)
    {
        $this->nameHash = $nameHash;

        return $this;
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
     * Set supplierId
     *
     * @param integer $supplierId
     *
     * @return SupplierProduct
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return SupplierProduct
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
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
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return SupplierProduct
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set article
     *
     * @param string $article
     *
     * @return SupplierProduct
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
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
     * Set code
     *
     * @param string $code
     *
     * @return SupplierProduct
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
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
     * Set brandId
     *
     * @param integer $brandId
     *
     * @return SupplierProduct
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
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
     * Set availabilityCode
     *
     * @param string $availabilityCode
     *
     * @return SupplierProduct
     */
    public function setAvailabilityCode($availabilityCode)
    {
        $this->availabilityCode = $availabilityCode;

        return $this;
    }

    /**
     * Get availabilityCode
     *
     * @return string
     */
    public function getAvailabilityCode()
    {
        return $this->availabilityCode;
    }

    /**
     * Set price
     * 
     * @param integer $price
     * 
     * @return SupplierProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
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
     * Set description
     * 
     * @param string $description
     * 
     * @return SupplierProduct
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     * Set isHidden
     * 
     * @param boolean $isHidden
     * 
     * @return SupplierProduct
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     * 
     * @return bool
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set model
     *
     * @param string $model
     *
     * @return SupplierProduct
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
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
     * Set priceRetailMin
     *
     * @param integer $priceRetailMin
     *
     * @return SupplierProduct
     */
    public function setPriceRetailMin($priceRetailMin)
    {
        $this->priceRetailMin = $priceRetailMin;

        return $this;
    }

    /**
     * Get priceRetailMin
     *
     * @return integer
     */
    public function getPriceRetailMin()
    {
        return $this->priceRetailMin;
    }

    /**
     * Set competitorPrice
     *
     * @param integer $competitorPrice
     *
     * @return SupplierProduct
     */
    public function setCompetitorPrice($competitorPrice)
    {
        $this->competitorPrice = $competitorPrice;

        return $this;
    }

    /**
     * Get competitorPrice
     *
     * @return integer
     */
    public function getCompetitorPrice()
    {
        return $this->competitorPrice;
    }

    /**
     * Set minQuantity
     *
     * @param integer $minQuantity
     *
     * @return SupplierProduct
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;

        return $this;
    }

    /**
     * Get minQuantity
     *
     * @return integer
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SupplierProduct
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return SupplierProduct
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set originalPrice
     *
     * @param integer $originalPrice
     *
     * @return SupplierProduct
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    /**
     * Get originalPrice
     *
     * @return integer
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * Set originalCurrency
     *
     * @param string $originalCurrency
     *
     * @return SupplierProduct
     */
    public function setOriginalCurrency($originalCurrency)
    {
        $this->originalCurrency = $originalCurrency;

        return $this;
    }

    /**
     * Get originalCurrency
     *
     * @return string
     */
    public function getOriginalCurrency()
    {
        return $this->originalCurrency;
    }
}

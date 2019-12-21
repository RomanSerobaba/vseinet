<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProduct.
 *
 * @ORM\Table(name="base_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BaseProductRepository")
 */
class BaseProduct
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
     * @ORM\Column(name="sef_url", type="string")
     */
    private $sefUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="category_section_id", type="integer")
     */
    private $sectionId;

    /**
     * @var int
     *
     * @ORM\Column(name="brand_id", type="integer", nullable=true)
     */
    private $brandId;

    /**
     * @var int
     *
     * @ORM\Column(name="color_composite_id", type="integer", nullable=true)
     */
    private $colorCompositeId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="min_quantity", type="integer")
     */
    private $minQuantity;

    /**
     * @var int
     *
     * @ORM\Column(name="estimate", type="integer")
     */
    private $estimate;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_price", type="integer")
     */
    private $supplierPrice;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_availability_code", type="string")
     */
    private $supplierAvailabilityCode;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="canonical_id", type="integer")
     */
    private $canonicalId;

    /**
     * @var int
     *
     * @ORM\Column(name="vat", type="integer")
     */
    private $vat;

    /**
     * @var int
     *
     * @ORM\Column(name="price_retail_min", type="integer")
     */
    private $priceRetailMin;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return BaseProduct
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sefUrl.
     *
     * @param string $sefUrl
     *
     * @return BaseProduct
     */
    public function setSefUrl($sefUrl)
    {
        $this->sefUrl = $sefUrl;

        return $this;
    }

    /**
     * Get sefUrl.
     *
     * @return string
     */
    public function getSefUrl()
    {
        return $this->sefUrl;
    }

    /**
     * Set categoryId.
     *
     * @param int $categoryId
     *
     * @return BaseProduct
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set sectionId.
     *
     * @param int $sectionId
     *
     * @return BaseProduct
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * Get sectionId.
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set brandId.
     *
     * @param int $brandId
     *
     * @return BaseProduct
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId.
     *
     * @return int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set colorCompositeId.
     *
     * @param int $colorCompositeId
     *
     * @return BaseProduct
     */
    public function setColorCompositeId($colorCompositeId)
    {
        $this->colorCompositeId = $colorCompositeId;

        return $this;
    }

    /**
     * Get colorCompositeId.
     *
     * @return int
     */
    public function getColorCompositeId()
    {
        return $this->colorCompositeId;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return BaseProduct
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return BaseProduct
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set minQuantity.
     *
     * @param int $minQuantity
     *
     * @return BaseProduct
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;

        return $this;
    }

    /**
     * Get minQuantity.
     *
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * Set estimate.
     *
     * @param int $estimate
     *
     * @return BaseProduct
     */
    public function setEstimate($estimate)
    {
        $this->estimate = $estimate;

        return $this;
    }

    /**
     * Get estimate.
     *
     * @return int
     */
    public function getEstimate()
    {
        return $this->estimate;
    }

    /**
     * Set supplierId.
     *
     * @param int $supplierId
     *
     * @return BaseProduct
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId.
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set supplierPrice.
     *
     * @param int $supplierPrice
     *
     * @return BaseProduct
     */
    public function setSupplierPrice($supplierPrice)
    {
        $this->supplierPrice = $supplierPrice;

        return $this;
    }

    /**
     * Get supplierPrice.
     *
     * @return int
     */
    public function getSupplierPrice()
    {
        return $this->supplierPrice;
    }

    /**
     * Set isHidden.
     *
     * @param bool $isHidden
     *
     * @return BaseProduct
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden.
     *
     * @return bool
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set supplierAvailabilityCode.
     *
     * @param string $supplierAvailabilityCode
     *
     * @return BaseProduct
     */
    public function setSupplierAvailabilityCode($supplierAvailabilityCode)
    {
        $this->supplierAvailabilityCode = $supplierAvailabilityCode;

        return $this;
    }

    /**
     * Get supplierAvailabilityCode.
     *
     * @return string
     */
    public function getSupplierAvailabilityCode()
    {
        return $this->supplierAvailabilityCode;
    }

    /**
     * Set rating.
     *
     * @param int $rating
     *
     * @return BaseProduct
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set canonicalId.
     *
     * @param int $canonicalId
     *
     * @return BaseProduct
     */
    public function setCanonicalId($canonicalId)
    {
        $this->canonicalId = $canonicalId;

        return $this;
    }

    /**
     * Get canonicalId.
     *
     * @return int
     */
    public function getCanonicalId()
    {
        return $this->canonicalId;
    }

    /**
     * Set vat.
     *
     * @param int $vat
     *
     * @return BaseProduct
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat.
     *
     * @return int
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set priceRetailMin.
     *
     * @param int $priceRetailMin
     *
     * @return BaseProduct
     */
    public function setPriceRetailMin($priceRetailMin)
    {
        $this->priceRetailMin = $priceRetailMin;

        return $this;
    }

    /**
     * Get priceRetailMin.
     *
     * @return int
     */
    public function getPriceRetailMin()
    {
        return $this->priceRetailMin;
    }
}

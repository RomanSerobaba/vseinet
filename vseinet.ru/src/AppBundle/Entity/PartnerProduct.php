<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PartnerProduct.
 *
 * @ORM\Table(name="partner_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PartnerProductRepository")
 */
class PartnerProduct
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
     * @var int
     *
     * @ORM\Column(name="partner_id", type="integer")
     */
    private $partnerId;

    /**
     * @var int
     *
     * @ORM\Column(name="partner_category_id", type="integer")
     */
    private $partnerCategoryId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="partner_pricelist_id", type="integer", nullable=true)
     */
    private $partnerPricelistId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="article", type="string", nullable=true)
     */
    private $article;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="brand_id", type="integer", nullable=true)
     */
    private $brandId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=22)
     */
    private $nash;

    /**
     * @var string
     *
     * @ORM\Column(name="hash_n1", type="string", length=22)
     */
    private $nashN1;

    /**
     * @var string
     *
     * @ORM\Column(name="hash_n2", type="string", length=22)
     */
    private $hashN2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_description", type="string", nullable=true)
     */
    private $shortDescription;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
     * Set partnerId.
     *
     * @param int $partnerId
     *
     * @return PartnerProduct
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;

        return $this;
    }

    /**
     * Get partnerId.
     *
     * @return int
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * Set partnerCategoryId.
     *
     * @param int $partnerCategoryId
     *
     * @return PartnerProduct
     */
    public function setPartnerCategoryId($partnerCategoryId)
    {
        $this->partnerCategoryId = $partnerCategoryId;

        return $this;
    }

    /**
     * Get partnerCategoryId.
     *
     * @return int
     */
    public function getPartnerCategoryId()
    {
        return $this->partnerCategoryId;
    }

    /**
     * Set partnerPricelistId.
     *
     * @param int|null $partnerPricelistId
     *
     * @return PartnerProduct
     */
    public function setPartnerPricelistId($partnerPricelistId = null)
    {
        $this->partnerPricelistId = $partnerPricelistId;

        return $this;
    }

    /**
     * Get partnerPricelistId.
     *
     * @return int|null
     */
    public function getPartnerPricelistId()
    {
        return $this->partnerPricelistId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return PartnerProduct
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
     * Set code.
     *
     * @param string|null $code
     *
     * @return PartnerProduct
     */
    public function setCode($code = null)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set article.
     *
     * @param string|null $article
     *
     * @return PartnerProduct
     */
    public function setArticle($article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article.
     *
     * @return string|null
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return PartnerProduct
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
     * Set brandId.
     *
     * @param int|null $brandId
     *
     * @return PartnerProduct
     */
    public function setBrandId($brandId = null)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId.
     *
     * @return int|null
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set baseProductId.
     *
     * @param int|null $baseProductId
     *
     * @return PartnerProduct
     */
    public function setBaseProductId($baseProductId = null)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int|null
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set hash.
     *
     * @param string $hash
     *
     * @return PartnerProduct
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set hashN1.
     *
     * @param string $hashN1
     *
     * @return PartnerProduct
     */
    public function setHashN1($hashN1)
    {
        $this->hashN1 = $hashN1;

        return $this;
    }

    /**
     * Get hashN1.
     *
     * @return string
     */
    public function getHashN1()
    {
        return $this->hashN1;
    }

    /**
     * Set hashN2.
     *
     * @param string $hashN2
     *
     * @return PartnerProduct
     */
    public function setHashN2($hashN2)
    {
        $this->hashN2 = $hashN2;

        return $this;
    }

    /**
     * Get hashN2.
     *
     * @return string
     */
    public function getHashN2()
    {
        return $this->hashN2;
    }

    /**
     * Set shortDescription.
     *
     * @param string|null $shortDescription
     *
     * @return PartnerProduct
     */
    public function setShortDescription($shortDescription = null)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription.
     *
     * @return string|null
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set isHidden.
     *
     * @param bool $isHidden
     *
     * @return PartnerProduct
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
}

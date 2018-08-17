<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BannerMainProductData
 *
 * @ORM\Table(name="banner_main_product_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BannerMainProductDataRepository")
 */
class BannerMainProductData
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
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="banner_id", type="integer")
     */
    private $bannerId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_pc", type="string", length=255, nullable=true)
     */
    private $titlePc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_tablet", type="string", length=255, nullable=true)
     */
    private $titleTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_phone", type="string", length=255, nullable=true)
     */
    private $titlePhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_pc", type="string", length=255, nullable=true)
     */
    private $photoPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_tablet", type="string", length=255, nullable=true)
     */
    private $photoTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_phone", type="string", length=255, nullable=true)
     */
    private $photoPhone;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sale_price", type="integer", nullable=true)
     */
    private $salePrice;


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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return BannerMainProductData
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set bannerId.
     *
     * @param int $bannerId
     *
     * @return BannerMainProductData
     */
    public function setBannerId($bannerId)
    {
        $this->bannerId = $bannerId;

        return $this;
    }

    /**
     * Get bannerId.
     *
     * @return int
     */
    public function getBannerId()
    {
        return $this->bannerId;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return BannerMainProductData
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set titlePc.
     *
     * @param string|null $titlePc
     *
     * @return BannerMainProductData
     */
    public function setTitlePc($titlePc = null)
    {
        $this->titlePc = $titlePc;

        return $this;
    }

    /**
     * Get titlePc.
     *
     * @return string|null
     */
    public function getTitlePc()
    {
        return $this->titlePc;
    }

    /**
     * Set titleTablet.
     *
     * @param string|null $titleTablet
     *
     * @return BannerMainProductData
     */
    public function setTitleTablet($titleTablet = null)
    {
        $this->titleTablet = $titleTablet;

        return $this;
    }

    /**
     * Get titleTablet.
     *
     * @return string|null
     */
    public function getTitleTablet()
    {
        return $this->titleTablet;
    }

    /**
     * Set titlePhone.
     *
     * @param string|null $titlePhone
     *
     * @return BannerMainProductData
     */
    public function setTitlePhone($titlePhone = null)
    {
        $this->titlePhone = $titlePhone;

        return $this;
    }

    /**
     * Get titlePhone.
     *
     * @return string|null
     */
    public function getTitlePhone()
    {
        return $this->titlePhone;
    }

    /**
     * Set photoPc.
     *
     * @param string|null $photoPc
     *
     * @return BannerMainProductData
     */
    public function setPhotoPc($photoPc = null)
    {
        $this->photoPc = $photoPc;

        return $this;
    }

    /**
     * Get photoPc.
     *
     * @return string|null
     */
    public function getPhotoPc()
    {
        return $this->photoPc;
    }

    /**
     * Set photoTablet.
     *
     * @param string|null $photoTablet
     *
     * @return BannerMainProductData
     */
    public function setPhotoTablet($photoTablet = null)
    {
        $this->photoTablet = $photoTablet;

        return $this;
    }

    /**
     * Get photoTablet.
     *
     * @return string|null
     */
    public function getPhotoTablet()
    {
        return $this->photoTablet;
    }

    /**
     * Set photoPhone.
     *
     * @param string|null $photoPhone
     *
     * @return BannerMainProductData
     */
    public function setPhotoPhone($photoPhone = null)
    {
        $this->photoPhone = $photoPhone;

        return $this;
    }

    /**
     * Get photoPhone.
     *
     * @return string|null
     */
    public function getPhotoPhone()
    {
        return $this->photoPhone;
    }

    /**
     * Set price.
     *
     * @param int|null $price
     *
     * @return BannerMainProductData
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set salePrice.
     *
     * @param int|null $salePrice
     *
     * @return BannerMainProductData
     */
    public function setSalePrice($salePrice = null)
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    /**
     * Get salePrice.
     *
     * @return int|null
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }
}

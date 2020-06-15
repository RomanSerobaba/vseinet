<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner.
 *
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BannerRepository")
 */
class Banner
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_since", type="datetime", nullable=true)
     */
    private $activeSince;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_till", type="datetime", nullable=true)
     */
    private $activeTill;

    /**
     * @var string
     *
     * @ORM\Column(name="image_pc", type="string", length=255)
     */
    private $imagePc;

    /**
     * @var string
     *
     * @ORM\Column(name="image_tablet", type="string", length=255)
     */
    private $imageTablet;

    /**
     * @var string
     *
     * @ORM\Column(name="image_phone", type="string", length=255)
     */
    private $imagePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="tab_background_color", type="string", length=15)
     */
    private $tabBackgroundColor;

    /**
     * @var string
     *
     * @ORM\Column(name="tab_text", type="string", length=255)
     */
    private $tabText;

    /**
     * @var string
     *
     * @ORM\Column(name="tab_text_color", type="string", length=255)
     */
    private $tabTextColor;

    /**
     * @var bool
     *
     * @ORM\Column(name="tab_is_fixed", type="boolean")
     */
    private $tabIsFixed;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var json|null
     *
     * @ORM\Column(name="base_products_ids", type="json", nullable=true)
     */
    private $baseProductsIds;

    /**
     * @var json|null
     *
     * @ORM\Column(name="categories_ids", type="json", nullable=true)
     */
    private $categoriesIds;

    /**
     * @var json|null
     *
     * @ORM\Column(name="brands_ids", type="json", nullable=true)
     */
    private $brandsIds;

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
     * @return Banner
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
     * Set url.
     *
     * @param string|null $url
     *
     * @return Banner
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set activeSince.
     *
     * @param \DateTime|null $activeSince
     *
     * @return Banner
     */
    public function setActiveSince($activeSince = null)
    {
        $this->activeSince = $activeSince;

        return $this;
    }

    /**
     * Get activeSince.
     *
     * @return \DateTime|null
     */
    public function getActiveSince()
    {
        return $this->activeSince;
    }

    /**
     * Set activeTill.
     *
     * @param \DateTime|null $activeTill
     *
     * @return Banner
     */
    public function setActiveTill($activeTill = null)
    {
        $this->activeTill = $activeTill;

        return $this;
    }

    /**
     * Get activeTill.
     *
     * @return \DateTime|null
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }

    /**
     * Set imagePc.
     *
     * @param string $imagePc
     *
     * @return Banner
     */
    public function setImagePc($imagePc)
    {
        $this->imagePc = $imagePc;

        return $this;
    }

    /**
     * Get imagePc.
     *
     * @return string
     */
    public function getImagePc()
    {
        return $this->imagePc;
    }

    /**
     * Set imageTablet.
     *
     * @param string $imageTablet
     *
     * @return Banner
     */
    public function setImageTablet($imageTablet)
    {
        $this->imageTablet = $imageTablet;

        return $this;
    }

    /**
     * Get imageTablet.
     *
     * @return string
     */
    public function getImageTablet()
    {
        return $this->imageTablet;
    }

    /**
     * Set imagePhone.
     *
     * @param string $imagePhone
     *
     * @return Banner
     */
    public function setImagePhone($imagePhone)
    {
        $this->imagePhone = $imagePhone;

        return $this;
    }

    /**
     * Get imagePhone.
     *
     * @return string
     */
    public function getImagePhone()
    {
        return $this->imagePhone;
    }

    /**
     * Set tabBackgroundColor.
     *
     * @param string $tabBackgroundColor
     *
     * @return Banner
     */
    public function setTabBackgroundColor($tabBackgroundColor)
    {
        $this->tabBackgroundColor = $tabBackgroundColor;

        return $this;
    }

    /**
     * Get tabBackgroundColor.
     *
     * @return string
     */
    public function getTabBackgroundColor()
    {
        return $this->tabBackgroundColor;
    }

    /**
     * Set tabText.
     *
     * @param string $tabText
     *
     * @return Banner
     */
    public function setTabText($tabText)
    {
        $this->tabText = $tabText;

        return $this;
    }

    /**
     * Get tabText.
     *
     * @return string
     */
    public function getTabText()
    {
        return $this->tabText;
    }

    /**
     * Set tabTextColor.
     *
     * @param string $tabTextColor
     *
     * @return Banner
     */
    public function setTabTextColor($tabTextColor)
    {
        $this->tabTextColor = $tabTextColor;

        return $this;
    }

    /**
     * Get tabTextColor.
     *
     * @return string
     */
    public function getTabTextColor()
    {
        return $this->tabTextColor;
    }

    /**
     * Set tabIsFixed.
     *
     * @param bool $tabIsFixed
     *
     * @return Banner
     */
    public function setTabIsFixed($tabIsFixed)
    {
        $this->tabIsFixed = $tabIsFixed;

        return $this;
    }

    /**
     * Get tabIsFixed.
     *
     * @return bool
     */
    public function getTabIsFixed()
    {
        return $this->tabIsFixed;
    }

    /**
     * Set priority.
     *
     * @param int $priority
     *
     * @return Banner
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set baseProductsIds.
     *
     * @param array|null $baseProductsIds
     *
     * @return Banner
     */
    public function setBaseProductsIds($baseProductsIds = null)
    {
        $this->baseProductsIds = $baseProductsIds;

        return $this;
    }

    /**
     * Get baseProductsIds.
     *
     * @return array|null
     */
    public function getBaseProductsIds()
    {
        return $this->baseProductsIds;
    }

    /**
     * Set categoriesIds.
     *
     * @param array|null $categoriesIds
     *
     * @return Banner
     */
    public function setCategoriesIds($categoriesIds = null)
    {
        $this->categoriesIds = $categoriesIds;

        return $this;
    }

    /**
     * Get categoriesIds.
     *
     * @return array|null
     */
    public function getCategoriesIds()
    {
        return $this->categoriesIds;
    }

    /**
     * Set brandsIds.
     *
     * @param array|null $brandsIds
     *
     * @return Banner
     */
    public function setBrandsIds($brandsIds = null)
    {
        $this->brandsIds = $brandsIds;

        return $this;
    }

    /**
     * Get brandsIds.
     *
     * @return array|null
     */
    public function getBrandsIds()
    {
        return $this->brandsIds;
    }
}

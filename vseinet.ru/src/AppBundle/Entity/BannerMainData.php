<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BannerMainData
 *
 * @ORM\Table(name="banner_main_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BannerMainDataRepository")
 */
class BannerMainData
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
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

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
     * @ORM\Column(name="img_background_pc", type="string", length=255, nullable=true)
     */
    private $imgBackgroundPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_background_tablet", type="string", length=255, nullable=true)
     */
    private $imgBackgroundTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_background_phone", type="string", length=255, nullable=true)
     */
    private $imgBackgroundPhone;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pos_background_pc_x", type="integer", nullable=true)
     */
    private $posBackgroundPcX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pos_background_pc_y", type="integer", nullable=true)
     */
    private $posBackgroundPcY;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pos_background_tablet_x", type="integer", nullable=true)
     */
    private $posBackgroundTabletX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pos_background_tablet_y", type="integer", nullable=true)
     */
    private $posBackgroundTabletY;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pos_background_phone_x", type="integer", nullable=true)
     */
    private $posBackgroundPhoneX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pos_background_phone_y", type="integer", nullable=true)
     */
    private $posBackgroundPhoneY;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_visible", type="boolean", nullable=true)
     */
    private $isVisible;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="start_visible_date", type="datetime", nullable=true)
     */
    private $startVisibleDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="end_visible_date", type="datetime", nullable=true)
     */
    private $endVisibleDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tab_background_color", type="string", length=255, nullable=true)
     */
    private $tabBackgroundColor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tab_text_color", type="string", length=255, nullable=true)
     */
    private $tabTextColor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tab_img", type="string", length=255, nullable=true)
     */
    private $tabImg;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tab_img_pos_x", type="integer", nullable=true)
     */
    private $tabImgPosX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tab_img_pos_y", type="integer", nullable=true)
     */
    private $tabImgPosY;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tab_text", type="string", length=255, nullable=true)
     */
    private $tabText;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="tab_is_fixed", type="boolean", nullable=true)
     */
    private $tabIsFixed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="background_color", type="string", length=255, nullable=true)
     */
    private $backgroundColor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_text_color", type="string", length=255, nullable=true)
     */
    private $titleTextColor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_pc", type="string", length=255, nullable=true)
     */
    private $imgPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_tablet", type="string", length=255, nullable=true)
     */
    private $imgTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_phone", type="string", length=255, nullable=true)
     */
    private $imgPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="img_pos_pc_x", type="integer", nullable=true)
     */
    private $imgPosPcX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="img_pos_pc_y", type="integer", nullable=true)
     */
    private $imgPosPcY;

    /**
     * @var int|null
     *
     * @ORM\Column(name="img_pos_tablet_x", type="integer", nullable=true)
     */
    private $imgPosTabletX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="img_pos_tablet_y", type="integer", nullable=true)
     */
    private $imgPosTabletY;

    /**
     * @var int|null
     *
     * @ORM\Column(name="img_pos_phone_x", type="integer", nullable=true)
     */
    private $imgPosPhoneX;

    /**
     * @var int|null
     *
     * @ORM\Column(name="img_pos_phone_y", type="integer", nullable=true)
     */
    private $imgPosPhoneY;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_pc", type="string", length=255, nullable=true)
     */
    private $descriptionPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_tablet", type="string", length=255, nullable=true)
     */
    private $descriptionTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_phone", type="string", length=255, nullable=true)
     */
    private $descriptionPhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price_pc", type="string", length=255, nullable=true)
     */
    private $pricePc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price_tablet", type="string", length=255, nullable=true)
     */
    private $priceTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price_phone", type="string", length=255, nullable=true)
     */
    private $pricePhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text_url_pc", type="string", length=255, nullable=true)
     */
    private $textUrlPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text_url_tablet", type="string", length=255, nullable=true)
     */
    private $textUrlTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text_url_phone", type="string", length=255, nullable=true)
     */
    private $textUrlPhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="left_details_pc", type="string", length=255, nullable=true)
     */
    private $leftDetailsPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="left_details_tablet", type="string", length=255, nullable=true)
     */
    private $leftDetailsTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="left_details_phone", type="string", length=255, nullable=true)
     */
    private $leftDetailsPhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="right_details_pc", type="string", length=255, nullable=true)
     */
    private $rightDetailsPc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="right_details_tablet", type="string", length=255, nullable=true)
     */
    private $rightDetailsTablet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="right_details_phone", type="string", length=255, nullable=true)
     */
    private $rightDetailsPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;

    /**
     * @var int
     *
     * @ORM\Column(name="templates_id", type="integer")
     */
    private $templatesId;

    /**
     * @ORM\Column(name="base_products_ids", type="json", nullable=true)
     *
     * @var string|null
     */
    private $baseProductsIds;

    /**
     * @ORM\Column(name="categories_ids", type="json", nullable=true)
     *
     * @var string|null
     */
    private $categoriesIds;

    /**
     * @ORM\Column(name="brands_ids", type="json", nullable=true)
     *
     * @var string|null
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
     * Set type.
     *
     * @param int $type
     *
     * @return BannerMainData
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return BannerMainData
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
     * @return BannerMainData
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
     * @return BannerMainData
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
     * @return BannerMainData
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
     * Set imgBackgroundPc.
     *
     * @param string|null $imgBackgroundPc
     *
     * @return BannerMainData
     */
    public function setImgBackgroundPc($imgBackgroundPc = null)
    {
        $this->imgBackgroundPc = $imgBackgroundPc;

        return $this;
    }

    /**
     * Get imgBackgroundPc.
     *
     * @return string|null
     */
    public function getImgBackgroundPc()
    {
        return $this->imgBackgroundPc;
    }

    /**
     * Set imgBackgroundTablet.
     *
     * @param string|null $imgBackgroundTablet
     *
     * @return BannerMainData
     */
    public function setImgBackgroundTablet($imgBackgroundTablet = null)
    {
        $this->imgBackgroundTablet = $imgBackgroundTablet;

        return $this;
    }

    /**
     * Get imgBackgroundTablet.
     *
     * @return string|null
     */
    public function getImgBackgroundTablet()
    {
        return $this->imgBackgroundTablet;
    }

    /**
     * Set imgBackgroundPhone.
     *
     * @param string|null $imgBackgroundPhone
     *
     * @return BannerMainData
     */
    public function setImgBackgroundPhone($imgBackgroundPhone = null)
    {
        $this->imgBackgroundPhone = $imgBackgroundPhone;

        return $this;
    }

    /**
     * Get imgBackgroundPhone.
     *
     * @return string|null
     */
    public function getImgBackgroundPhone()
    {
        return $this->imgBackgroundPhone;
    }

    /**
     * Set posBackgroundPcX.
     *
     * @param int|null $posBackgroundPcX
     *
     * @return BannerMainData
     */
    public function setPosBackgroundPcX($posBackgroundPcX = null)
    {
        $this->posBackgroundPcX = $posBackgroundPcX;

        return $this;
    }

    /**
     * Get posBackgroundPcX.
     *
     * @return int|null
     */
    public function getPosBackgroundPcX()
    {
        return $this->posBackgroundPcX;
    }

    /**
     * Set posBackgroundPcY.
     *
     * @param int|null $posBackgroundPcY
     *
     * @return BannerMainData
     */
    public function setPosBackgroundPcY($posBackgroundPcY = null)
    {
        $this->posBackgroundPcY = $posBackgroundPcY;

        return $this;
    }

    /**
     * Get posBackgroundPcY.
     *
     * @return int|null
     */
    public function getPosBackgroundPcY()
    {
        return $this->posBackgroundPcY;
    }

    /**
     * Set posBackgroundTabletX.
     *
     * @param int|null $posBackgroundTabletX
     *
     * @return BannerMainData
     */
    public function setPosBackgroundTabletX($posBackgroundTabletX = null)
    {
        $this->posBackgroundTabletX = $posBackgroundTabletX;

        return $this;
    }

    /**
     * Get posBackgroundTabletX.
     *
     * @return int|null
     */
    public function getPosBackgroundTabletX()
    {
        return $this->posBackgroundTabletX;
    }

    /**
     * Set posBackgroundTabletY.
     *
     * @param int|null $posBackgroundTabletY
     *
     * @return BannerMainData
     */
    public function setPosBackgroundTabletY($posBackgroundTabletY = null)
    {
        $this->posBackgroundTabletY = $posBackgroundTabletY;

        return $this;
    }

    /**
     * Get posBackgroundTabletY.
     *
     * @return int|null
     */
    public function getPosBackgroundTabletY()
    {
        return $this->posBackgroundTabletY;
    }

    /**
     * Set posBackgroundPhoneX.
     *
     * @param int|null $posBackgroundPhoneX
     *
     * @return BannerMainData
     */
    public function setPosBackgroundPhoneX($posBackgroundPhoneX = null)
    {
        $this->posBackgroundPhoneX = $posBackgroundPhoneX;

        return $this;
    }

    /**
     * Get posBackgroundPhoneX.
     *
     * @return int|null
     */
    public function getPosBackgroundPhoneX()
    {
        return $this->posBackgroundPhoneX;
    }

    /**
     * Set posBackgroundPhoneY.
     *
     * @param int|null $posBackgroundPhoneY
     *
     * @return BannerMainData
     */
    public function setPosBackgroundPhoneY($posBackgroundPhoneY = null)
    {
        $this->posBackgroundPhoneY = $posBackgroundPhoneY;

        return $this;
    }

    /**
     * Get posBackgroundPhoneY.
     *
     * @return int|null
     */
    public function getPosBackgroundPhoneY()
    {
        return $this->posBackgroundPhoneY;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return BannerMainData
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
     * Set isVisible.
     *
     * @param bool|null $isVisible
     *
     * @return BannerMainData
     */
    public function setIsVisible($isVisible = null)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get isVisible.
     *
     * @return bool|null
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set startVisibleDate.
     *
     * @param \DateTime|null $startVisibleDate
     *
     * @return BannerMainData
     */
    public function setStartVisibleDate($startVisibleDate = null)
    {
        $this->startVisibleDate = $startVisibleDate;

        return $this;
    }

    /**
     * Get startVisibleDate.
     *
     * @return \DateTime|null
     */
    public function getStartVisibleDate()
    {
        return $this->startVisibleDate;
    }

    /**
     * Set endVisibleDate.
     *
     * @param \DateTime|null $endVisibleDate
     *
     * @return BannerMainData
     */
    public function setEndVisibleDate($endVisibleDate = null)
    {
        $this->endVisibleDate = $endVisibleDate;

        return $this;
    }

    /**
     * Get endVisibleDate.
     *
     * @return \DateTime|null
     */
    public function getEndVisibleDate()
    {
        return $this->endVisibleDate;
    }

    /**
     * Set tabBackgroundColor.
     *
     * @param string|null $tabBackgroundColor
     *
     * @return BannerMainData
     */
    public function setTabBackgroundColor($tabBackgroundColor = null)
    {
        $this->tabBackgroundColor = $tabBackgroundColor;

        return $this;
    }

    /**
     * Get tabBackgroundColor.
     *
     * @return string|null
     */
    public function getTabBackgroundColor()
    {
        return $this->tabBackgroundColor;
    }

    /**
     * Set tabTextColor.
     *
     * @param string|null $tabTextColor
     *
     * @return BannerMainData
     */
    public function setTabTextColor($tabTextColor = null)
    {
        $this->tabTextColor = $tabTextColor;

        return $this;
    }

    /**
     * Get tabTextColor.
     *
     * @return string|null
     */
    public function getTabTextColor()
    {
        return $this->tabTextColor;
    }

    /**
     * Set tabImg.
     *
     * @param string|null $tabImg
     *
     * @return BannerMainData
     */
    public function setTabImg($tabImg = null)
    {
        $this->tabImg = $tabImg;

        return $this;
    }

    /**
     * Get tabImg.
     *
     * @return string|null
     */
    public function getTabImg()
    {
        return $this->tabImg;
    }

    /**
     * Set tabImgPosX.
     *
     * @param int|null $tabImgPosX
     *
     * @return BannerMainData
     */
    public function setTabImgPosX($tabImgPosX = null)
    {
        $this->tabImgPosX = $tabImgPosX;

        return $this;
    }

    /**
     * Get tabImgPosX.
     *
     * @return int|null
     */
    public function getTabImgPosX()
    {
        return $this->tabImgPosX;
    }

    /**
     * Set tabImgPosY.
     *
     * @param int|null $tabImgPosY
     *
     * @return BannerMainData
     */
    public function setTabImgPosY($tabImgPosY = null)
    {
        $this->tabImgPosY = $tabImgPosY;

        return $this;
    }

    /**
     * Get tabImgPosY.
     *
     * @return int|null
     */
    public function getTabImgPosY()
    {
        return $this->tabImgPosY;
    }

    /**
     * Set tabText.
     *
     * @param string|null $tabText
     *
     * @return BannerMainData
     */
    public function setTabText($tabText = null)
    {
        $this->tabText = $tabText;

        return $this;
    }

    /**
     * Get tabText.
     *
     * @return string|null
     */
    public function getTabText()
    {
        return $this->tabText;
    }

    /**
     * Set tabIsFixed.
     *
     * @param bool|null $tabIsFixed
     *
     * @return BannerMainData
     */
    public function setTabIsFixed($tabIsFixed = null)
    {
        $this->tabIsFixed = $tabIsFixed;

        return $this;
    }

    /**
     * Get tabIsFixed.
     *
     * @return bool|null
     */
    public function getTabIsFixed()
    {
        return $this->tabIsFixed;
    }

    /**
     * Set backgroundColor.
     *
     * @param string|null $backgroundColor
     *
     * @return BannerMainData
     */
    public function setBackgroundColor($backgroundColor = null)
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Get backgroundColor.
     *
     * @return string|null
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Set titleTextColor.
     *
     * @param string|null $titleTextColor
     *
     * @return BannerMainData
     */
    public function setTitleTextColor($titleTextColor = null)
    {
        $this->titleTextColor = $titleTextColor;

        return $this;
    }

    /**
     * Get titleTextColor.
     *
     * @return string|null
     */
    public function getTitleTextColor()
    {
        return $this->titleTextColor;
    }

    /**
     * Set imgPc.
     *
     * @param string|null $imgPc
     *
     * @return BannerMainData
     */
    public function setImgPc($imgPc = null)
    {
        $this->imgPc = $imgPc;

        return $this;
    }

    /**
     * Get imgPc.
     *
     * @return string|null
     */
    public function getImgPc()
    {
        return $this->imgPc;
    }

    /**
     * Set imgTablet.
     *
     * @param string|null $imgTablet
     *
     * @return BannerMainData
     */
    public function setImgTablet($imgTablet = null)
    {
        $this->imgTablet = $imgTablet;

        return $this;
    }

    /**
     * Get imgTablet.
     *
     * @return string|null
     */
    public function getImgTablet()
    {
        return $this->imgTablet;
    }

    /**
     * Set imgPhone.
     *
     * @param string|null $imgPhone
     *
     * @return BannerMainData
     */
    public function setImgPhone($imgPhone = null)
    {
        $this->imgPhone = $imgPhone;

        return $this;
    }

    /**
     * Get imgPhone.
     *
     * @return string|null
     */
    public function getImgPhone()
    {
        return $this->imgPhone;
    }

    /**
     * Set imgPosPcX.
     *
     * @param int|null $imgPosPcX
     *
     * @return BannerMainData
     */
    public function setImgPosPcX($imgPosPcX = null)
    {
        $this->imgPosPcX = $imgPosPcX;

        return $this;
    }

    /**
     * Get imgPosPcX.
     *
     * @return int|null
     */
    public function getImgPosPcX()
    {
        return $this->imgPosPcX;
    }

    /**
     * Set imgPosPcY.
     *
     * @param int|null $imgPosPcY
     *
     * @return BannerMainData
     */
    public function setImgPosPcY($imgPosPcY = null)
    {
        $this->imgPosPcY = $imgPosPcY;

        return $this;
    }

    /**
     * Get imgPosPcY.
     *
     * @return int|null
     */
    public function getImgPosPcY()
    {
        return $this->imgPosPcY;
    }

    /**
     * Set imgPosTabletX.
     *
     * @param int|null $imgPosTabletX
     *
     * @return BannerMainData
     */
    public function setImgPosTabletX($imgPosTabletX = null)
    {
        $this->imgPosTabletX = $imgPosTabletX;

        return $this;
    }

    /**
     * Get imgPosTabletX.
     *
     * @return int|null
     */
    public function getImgPosTabletX()
    {
        return $this->imgPosTabletX;
    }

    /**
     * Set imgPosTabletY.
     *
     * @param int|null $imgPosTabletY
     *
     * @return BannerMainData
     */
    public function setImgPosTabletY($imgPosTabletY = null)
    {
        $this->imgPosTabletY = $imgPosTabletY;

        return $this;
    }

    /**
     * Get imgPosTabletY.
     *
     * @return int|null
     */
    public function getImgPosTabletY()
    {
        return $this->imgPosTabletY;
    }

    /**
     * Set imgPosPhoneX.
     *
     * @param int|null $imgPosPhoneX
     *
     * @return BannerMainData
     */
    public function setImgPosPhoneX($imgPosPhoneX = null)
    {
        $this->imgPosPhoneX = $imgPosPhoneX;

        return $this;
    }

    /**
     * Get imgPosPhoneX.
     *
     * @return int|null
     */
    public function getImgPosPhoneX()
    {
        return $this->imgPosPhoneX;
    }

    /**
     * Set imgPosPhoneY.
     *
     * @param int|null $imgPosPhoneY
     *
     * @return BannerMainData
     */
    public function setImgPosPhoneY($imgPosPhoneY = null)
    {
        $this->imgPosPhoneY = $imgPosPhoneY;

        return $this;
    }

    /**
     * Get imgPosPhoneY.
     *
     * @return int|null
     */
    public function getImgPosPhoneY()
    {
        return $this->imgPosPhoneY;
    }

    /**
     * Set descriptionPc.
     *
     * @param string|null $descriptionPc
     *
     * @return BannerMainData
     */
    public function setDescriptionPc($descriptionPc = null)
    {
        $this->descriptionPc = $descriptionPc;

        return $this;
    }

    /**
     * Get descriptionPc.
     *
     * @return string|null
     */
    public function getDescriptionPc()
    {
        return $this->descriptionPc;
    }

    /**
     * Set descriptionTablet.
     *
     * @param string|null $descriptionTablet
     *
     * @return BannerMainData
     */
    public function setDescriptionTablet($descriptionTablet = null)
    {
        $this->descriptionTablet = $descriptionTablet;

        return $this;
    }

    /**
     * Get descriptionTablet.
     *
     * @return string|null
     */
    public function getDescriptionTablet()
    {
        return $this->descriptionTablet;
    }

    /**
     * Set descriptionPhone.
     *
     * @param string|null $descriptionPhone
     *
     * @return BannerMainData
     */
    public function setDescriptionPhone($descriptionPhone = null)
    {
        $this->descriptionPhone = $descriptionPhone;

        return $this;
    }

    /**
     * Get descriptionPhone.
     *
     * @return string|null
     */
    public function getDescriptionPhone()
    {
        return $this->descriptionPhone;
    }

    /**
     * Set pricePc.
     *
     * @param string|null $pricePc
     *
     * @return BannerMainData
     */
    public function setPricePc($pricePc = null)
    {
        $this->pricePc = $pricePc;

        return $this;
    }

    /**
     * Get pricePc.
     *
     * @return string|null
     */
    public function getPricePc()
    {
        return $this->pricePc;
    }

    /**
     * Set priceTablet.
     *
     * @param string|null $priceTablet
     *
     * @return BannerMainData
     */
    public function setPriceTablet($priceTablet = null)
    {
        $this->priceTablet = $priceTablet;

        return $this;
    }

    /**
     * Get priceTablet.
     *
     * @return string|null
     */
    public function getPriceTablet()
    {
        return $this->priceTablet;
    }

    /**
     * Set pricePhone.
     *
     * @param string|null $pricePhone
     *
     * @return BannerMainData
     */
    public function setPricePhone($pricePhone = null)
    {
        $this->pricePhone = $pricePhone;

        return $this;
    }

    /**
     * Get pricePhone.
     *
     * @return string|null
     */
    public function getPricePhone()
    {
        return $this->pricePhone;
    }

    /**
     * Set textUrlPc.
     *
     * @param string|null $textUrlPc
     *
     * @return BannerMainData
     */
    public function setTextUrlPc($textUrlPc = null)
    {
        $this->textUrlPc = $textUrlPc;

        return $this;
    }

    /**
     * Get textUrlPc.
     *
     * @return string|null
     */
    public function getTextUrlPc()
    {
        return $this->textUrlPc;
    }

    /**
     * Set textUrlTablet.
     *
     * @param string|null $textUrlTablet
     *
     * @return BannerMainData
     */
    public function setTextUrlTablet($textUrlTablet = null)
    {
        $this->textUrlTablet = $textUrlTablet;

        return $this;
    }

    /**
     * Get textUrlTablet.
     *
     * @return string|null
     */
    public function getTextUrlTablet()
    {
        return $this->textUrlTablet;
    }

    /**
     * Set textUrlPhone.
     *
     * @param string|null $textUrlPhone
     *
     * @return BannerMainData
     */
    public function setTextUrlPhone($textUrlPhone = null)
    {
        $this->textUrlPhone = $textUrlPhone;

        return $this;
    }

    /**
     * Get textUrlPhone.
     *
     * @return string|null
     */
    public function getTextUrlPhone()
    {
        return $this->textUrlPhone;
    }

    /**
     * Set leftDetailsPc.
     *
     * @param string|null $leftDetailsPc
     *
     * @return BannerMainData
     */
    public function setLeftDetailsPc($leftDetailsPc = null)
    {
        $this->leftDetailsPc = $leftDetailsPc;

        return $this;
    }

    /**
     * Get leftDetailsPc.
     *
     * @return string|null
     */
    public function getLeftDetailsPc()
    {
        return $this->leftDetailsPc;
    }

    /**
     * Set leftDetailsTablet.
     *
     * @param string|null $leftDetailsTablet
     *
     * @return BannerMainData
     */
    public function setLeftDetailsTablet($leftDetailsTablet = null)
    {
        $this->leftDetailsTablet = $leftDetailsTablet;

        return $this;
    }

    /**
     * Get leftDetailsTablet.
     *
     * @return string|null
     */
    public function getLeftDetailsTablet()
    {
        return $this->leftDetailsTablet;
    }

    /**
     * Set leftDetailsPhone.
     *
     * @param string|null $leftDetailsPhone
     *
     * @return BannerMainData
     */
    public function setLeftDetailsPhone($leftDetailsPhone = null)
    {
        $this->leftDetailsPhone = $leftDetailsPhone;

        return $this;
    }

    /**
     * Get leftDetailsPhone.
     *
     * @return string|null
     */
    public function getLeftDetailsPhone()
    {
        return $this->leftDetailsPhone;
    }

    /**
     * Set rightDetailsPc.
     *
     * @param string|null $rightDetailsPc
     *
     * @return BannerMainData
     */
    public function setRightDetailsPc($rightDetailsPc = null)
    {
        $this->rightDetailsPc = $rightDetailsPc;

        return $this;
    }

    /**
     * Get rightDetailsPc.
     *
     * @return string|null
     */
    public function getRightDetailsPc()
    {
        return $this->rightDetailsPc;
    }

    /**
     * Set rightDetailsTablet.
     *
     * @param string|null $rightDetailsTablet
     *
     * @return BannerMainData
     */
    public function setRightDetailsTablet($rightDetailsTablet = null)
    {
        $this->rightDetailsTablet = $rightDetailsTablet;

        return $this;
    }

    /**
     * Get rightDetailsTablet.
     *
     * @return string|null
     */
    public function getRightDetailsTablet()
    {
        return $this->rightDetailsTablet;
    }

    /**
     * Set rightDetailsPhone.
     *
     * @param string|null $rightDetailsPhone
     *
     * @return BannerMainData
     */
    public function setRightDetailsPhone($rightDetailsPhone = null)
    {
        $this->rightDetailsPhone = $rightDetailsPhone;

        return $this;
    }

    /**
     * Get rightDetailsPhone.
     *
     * @return string|null
     */
    public function getRightDetailsPhone()
    {
        return $this->rightDetailsPhone;
    }

    /**
     * Set weight.
     *
     * @param int $weight
     *
     * @return BannerMainData
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set templatesId.
     *
     * @param int $templatesId
     *
     * @return BannerMainData
     */
    public function setTemplatesId($templatesId)
    {
        $this->templatesId = $templatesId;

        return $this;
    }

    /**
     * Get templatesId.
     *
     * @return int
     */
    public function getTemplatesId()
    {
        return $this->templatesId;
    }

    /**
     * Set baseProductsIds.
     *
     * @param array|null $baseProductsIds
     *
     * @return BannerMainData
     */
    public function setBaseProductsIds($baseProductsIds = null): self
    {
        $this->baseProductsIds = $baseProductsIds;

        return $this;
    }

    /**
     * Get baseProductsIds.
     */
    public function getBaseProductsIds(): ?array
    {
        return $this->baseProductsIds;
    }

    /**
     * Set categoriesIds.
     *
     * @param array|null $categoriesIds
     *
     * @return BannerMainData
     */
    public function setCategoriesIds($categoriesIds = null): self
    {
        $this->categoriesIds = $categoriesIds;

        return $this;
    }

    /**
     * Get categoriesIds.
     */
    public function getCategoriesIds(): ?array
    {
        return $this->categoriesIds;
    }

    /**
     * Set brandsIds.
     *
     * @param array|null $brandsIds
     *
     * @return BannerMainData
     */
    public function setBrandsIds($brandsIds = null): self
    {
        $this->brandsIds = $brandsIds;

        return $this;
    }

    /**
     * Get brandsIds.
     */
    public function getBrandsIds(): ?array
    {
        return $this->brandsIds;
    }
}

<?php 

namespace ShopBundle\Bus\Banner\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Banner
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @Assert\Type(type="integer")
     */
    public $type;
    
    /**
     * @Assert\Type(type="integer")
     */
    public $weight;

    /**
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isVisible;

    /**
     * @Assert\Type(type="boolean")
     */
    public $tabIsFixed;

    /**
     * @Assert\Type(type="datetime")
     */
    public $startVisibleDate;

    /**
     * @Assert\Type(type="datetime")
     */
    public $endVisibleDate;

    /**
     * @Assert\Type(type="string")
     */
    public $backgroundColor;

    /**
     * @Assert\Type(type="string")
     */
    public $titleTextColor;

    /**
     * @Assert\Type(type="integer")
     */
    public $templatesId;

    /**
     * @Assert\Type(type="string")
     */
    public $tabBackgroundColor;

    /**
     * @Assert\Type(type="string")
     */
    public $tabTextColor;

    /**
     * @Assert\Type(type="string")
     */
    public $tabImg;

    /**
     * @Assert\Type(type="string")
     */
    public $tabText;

    /**
     * @Assert\Type(type="string")
     */
    public $imgBackgroundPc;

    /**
     * @Assert\Type(type="string")
     */
    public $imgBackgroundTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $imgBackgroundPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $descriptionPc;

    /**
     * @Assert\Type(type="string")
     */
    public $descriptionTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $descriptionPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $titlePc;

    /**
     * @Assert\Type(type="string")
     */
    public $titleTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $titlePhone;

    /**
     * @Assert\Type(type="string")
     */
    public $textUrlPc;

    /**
     * @Assert\Type(type="string")
     */
    public $textUrlTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $textUrlPhone;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPcX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPcY;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundTabletX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundTabletY;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPhoneX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPhoneY;

    /**
     * @Assert\Type(type="string")
     */
    public $leftDetailsPc;
    
    /**
     * @Assert\Type(type="string")
     */
    public $leftDetailsTablet;
    
    /**
     * @Assert\Type(type="string")
     */
    public $leftDetailsPhone;
    
    /**
     * @Assert\Type(type="string")
     */
    public $rightDetailsPc;
    
    /**
     * @Assert\Type(type="string")
     */
    public $rightDetailsTablet;
    
    /**
     * @Assert\Type(type="string")
     */
    public $rightDetailsPhone;

    /**
     * @Assert\Type(type="array<ShopBundle\Bus\Banner\Query\DTO\ProductData>")
     */
    public $productList;

    /**
     * Banner constructor.
     * @param $id
     * @param $type
     * @param $weight
     * @param $url
     * @param $title
     * @param $isVisible
     * @param $tabIsFixed
     * @param $startVisibleDate
     * @param $endVisibleDate
     * @param $backgroundColor
     * @param $titleTextColor
     * @param $templatesId
     * @param $tabBackgroundColor
     * @param $tabTextColor
     * @param $tabImg
     * @param $tabText
     * @param $imgBackgroundPc
     * @param $imgBackgroundTablet
     * @param $imgBackgroundPhone
     * @param $descriptionPc
     * @param $descriptionTablet
     * @param $descriptionPhone
     * @param $titlePc
     * @param $titleTablet
     * @param $titlePhone
     * @param $textUrlPc
     * @param $textUrlTablet
     * @param $textUrlPhone
     * @param $posBackgroundPcX
     * @param $posBackgroundPcY
     * @param $posBackgroundTabletX
     * @param $posBackgroundTabletY
     * @param $posBackgroundPhoneX
     * @param $posBackgroundPhoneY
     * @param $leftDetailsPc
     * @param $leftDetailsTablet
     * @param $leftDetailsPhone
     * @param $rightDetailsPc
     * @param $rightDetailsTablet
     * @param $rightDetailsPhone
     * @param array $productList
     */
    public function __construct($id, $type, $weight, $url, $title, $isVisible, $tabIsFixed, $startVisibleDate, $endVisibleDate, $backgroundColor, $titleTextColor, $templatesId, $tabBackgroundColor, $tabTextColor, $tabImg, $tabText, $imgBackgroundPc, $imgBackgroundTablet, $imgBackgroundPhone, $descriptionPc, $descriptionTablet, $descriptionPhone, $titlePc, $titleTablet, $titlePhone, $textUrlPc, $textUrlTablet, $textUrlPhone, $posBackgroundPcX, $posBackgroundPcY, $posBackgroundTabletX, $posBackgroundTabletY, $posBackgroundPhoneX, $posBackgroundPhoneY, $leftDetailsPc, $leftDetailsTablet, $leftDetailsPhone, $rightDetailsPc, $rightDetailsTablet, $rightDetailsPhone, $productList=[])
    {
        $this->id = $id;
        $this->type = $type;
        $this->weight = $weight;
        $this->url = $url;
        $this->title = $title;
        $this->isVisible = $isVisible;
        $this->tabIsFixed = $tabIsFixed;
        $this->startVisibleDate = $startVisibleDate;
        $this->endVisibleDate = $endVisibleDate;
        $this->backgroundColor = $backgroundColor;
        $this->titleTextColor = $titleTextColor;
        $this->templatesId = $templatesId;
        $this->tabBackgroundColor = $tabBackgroundColor;
        $this->tabTextColor = $tabTextColor;
        $this->tabImg = $tabImg;
        $this->tabText = $tabText;
        $this->imgBackgroundPc = $imgBackgroundPc;
        $this->imgBackgroundTablet = $imgBackgroundTablet;
        $this->imgBackgroundPhone = $imgBackgroundPhone;
        $this->descriptionPc = $descriptionPc;
        $this->descriptionTablet = $descriptionTablet;
        $this->descriptionPhone = $descriptionPhone;
        $this->titlePc = $titlePc;
        $this->titleTablet = $titleTablet;
        $this->titlePhone = $titlePhone;
        $this->textUrlPc = $textUrlPc;
        $this->textUrlTablet = $textUrlTablet;
        $this->textUrlPhone = $textUrlPhone;
        $this->posBackgroundPcX = $posBackgroundPcX;
        $this->posBackgroundPcY = $posBackgroundPcY;
        $this->posBackgroundTabletX = $posBackgroundTabletX;
        $this->posBackgroundTabletY = $posBackgroundTabletY;
        $this->posBackgroundPhoneX = $posBackgroundPhoneX;
        $this->posBackgroundPhoneY = $posBackgroundPhoneY;
        $this->leftDetailsPc = $leftDetailsPc;
        $this->leftDetailsTablet = $leftDetailsTablet;
        $this->leftDetailsPhone = $leftDetailsPhone;
        $this->rightDetailsPc = $rightDetailsPc;
        $this->rightDetailsTablet = $rightDetailsTablet;
        $this->rightDetailsPhone = $rightDetailsPhone;
        $this->productList = $productList;
    }
}
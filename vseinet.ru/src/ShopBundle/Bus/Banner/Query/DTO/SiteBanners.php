<?php 

namespace ShopBundle\Bus\Banner\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SiteBanners
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
     * @Assert\Type(type="array<ShopBundle\Bus\Banner\Query\DTO\ProductData>")
     */
    public $productList;

    /**
     * SiteBanners constructor.
     * @param $id
     * @param $type
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
     * @param array $productList
     */
    public function __construct($id, $type, $url, $title, $isVisible, $tabIsFixed, $startVisibleDate, $endVisibleDate, $backgroundColor, $titleTextColor, $templatesId, $tabBackgroundColor, $tabTextColor, $tabImg, $tabText, $productList=[])
    {
        $this->id = $id;
        $this->type = $type;
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
        $this->productList = $productList;
    }
}
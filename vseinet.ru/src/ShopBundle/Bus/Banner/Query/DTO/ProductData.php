<?php 

namespace ShopBundle\Bus\Banner\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductData
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     */
    public $bannerId;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

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
    public $photoPc;

    /**
     * @Assert\Type(type="string")
     */
    public $photoTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $photoPhone;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="integer")
     */
    public $salePrice;

    /**
     * ProductData constructor.
     * @param $id
     * @param $baseProductId
     * @param $bannerId
     * @param $title
     * @param $titlePc
     * @param $titleTablet
     * @param $titlePhone
     * @param $photoPc
     * @param $photoTablet
     * @param $photoPhone
     * @param $price
     * @param $salePrice
     */
    public function __construct($id, $baseProductId, $bannerId, $title, $titlePc, $titleTablet, $titlePhone, $photoPc, $photoTablet, $photoPhone, $price, $salePrice)
    {
        $this->id = $id;
        $this->baseProductId = $baseProductId;
        $this->bannerId = $bannerId;
        $this->title = $title;
        $this->titlePc = $titlePc;
        $this->titleTablet = $titleTablet;
        $this->titlePhone = $titlePhone;
        $this->photoPc = $photoPc;
        $this->photoTablet = $photoTablet;
        $this->photoPhone = $photoPhone;
        $this->price = $price;
        $this->salePrice = $salePrice;
    }
}
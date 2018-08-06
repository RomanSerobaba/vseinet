<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SupplierProduct
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $availabilityCode;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Цена в копейках")
     */
    public $price;

    /**
     * @Assert\Type(type="string")
     */
    public $brand;

    /**
     * @Assert\Type(type="text")
     */
    public $description;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isHidden;

    /**
     * @Assert\Type(type="string")
     */
    public $barCodes;


    public function __construct($id, $name, $categoryId, $code, $availabilityCode, $price, $brand, $description, $isHidden, $barCodes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->code = $code;
        $this->availabilityCode = $availabilityCode;
        $this->price = $price;
        $this->brand = $brand;
        $this->description = $description;
        $this->isHidden = $isHidden;
        $this->barCodes = $barCodes ?: null;
    }
}
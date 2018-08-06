<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class BaseProduct
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
     * @Assert\Type(type="integer")
     * @VIA\Description("Цена в копейках")
     */
    public $price;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Путь к основному изображению")
     */
    public $imageSrc;

    /**
     * @Assert\Choice({"new", "active", "old"}, strict=true)
     */
    public $state;


    public function __construct($id, $name, $categoryId, $price, $imageSrc, $state)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->price = $price;
        $this->imageSrc = $imageSrc;
        $this->state = $state;
    }
}
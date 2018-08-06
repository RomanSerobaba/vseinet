<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BaseProductForOrder
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
     */
    public $needQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $soldQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $orderedQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    public function __construct($id, $name, $categoryId, $needQuantity = 0, $soldQuantity = 0, $orderedQuantity = 0, $purchasePrice = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->needQuantity = $needQuantity;
        $this->soldQuantity = $soldQuantity;
        $this->orderedQuantity = $orderedQuantity;
        $this->purchasePrice = $purchasePrice;
    }
}
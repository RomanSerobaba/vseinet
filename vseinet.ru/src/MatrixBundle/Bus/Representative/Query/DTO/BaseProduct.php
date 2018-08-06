<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

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
     */
    public $matrixQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $reserveQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $transitQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $orderedQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $soldQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    public function __construct($id, $name, $categoryId, $matrixQuantity, $reserveQuantity, $transitQuantity, $orderedQuantity, $soldQuantity, $purchasePrice = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->matrixQuantity = $matrixQuantity;
        $this->reserveQuantity = $reserveQuantity;
        $this->transitQuantity = $transitQuantity;
        $this->orderedQuantity = $orderedQuantity;
        $this->soldQuantity = $soldQuantity;
        $this->purchasePrice = $purchasePrice;
    }
}
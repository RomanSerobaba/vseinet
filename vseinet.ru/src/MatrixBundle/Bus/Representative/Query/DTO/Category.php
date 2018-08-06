<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Category
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
    public $matrixQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

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
     * @Assert\Type(type="array")
     */
    public $childrenIds;

    /**
     * @Assert\Type(type="array")
     */
    public $productsIds;

    public function __construct($id, $name, $matrixQuantity = 0, $pid = null, $reserveQuantity = 0, $transitQuantity = 0, $orderedQuantity = 0, $soldQuantity = 0, $childrenIds = [], $productsIds = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->matrixQuantity = $matrixQuantity;
        $this->reserveQuantity = $reserveQuantity;
        $this->transitQuantity = $transitQuantity;
        $this->orderedQuantity = $orderedQuantity;
        $this->soldQuantity = $soldQuantity;
        $this->pid = $pid;
        $this->childrenIds = $childrenIds;
        $this->productsIds = $productsIds;
    }
}
<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CategoryForOrder
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
    public $needQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="array")
     */
    public $childrenIds;

    /**
     * @Assert\Type(type="array")
     */
    public $productsIds;

    public function __construct($id, $name, $needQuantity = 0, $pid = null, $childrenIds = [], $productsIds = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->needQuantity = $needQuantity;
        $this->pid = $pid;
        $this->childrenIds = $childrenIds;
        $this->productsIds = $productsIds;
    }
}
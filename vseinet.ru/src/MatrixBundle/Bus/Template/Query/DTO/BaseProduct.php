<?php 

namespace MatrixBundle\Bus\Template\Query\DTO;

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
    public $purchasePrice;

    public function __construct($id, $name, $categoryId, $matrixQuantity, $purchasePrice = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->matrixQuantity = $matrixQuantity;
        $this->purchasePrice = $purchasePrice;
    }
}
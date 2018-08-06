<?php 

namespace SupplyBundle\Bus\LowCostPurchases\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Products
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
    public $categoryName;

    /**
     * @Assert\Type(type="integer")
     */
    public $productId;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierPrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierCode1;

    /**
     * @Assert\Type(type="integer")
     */
    public $prevPrice;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierCode2;

    /**
     * @Assert\Type(type="integer")
     */
    public $prc;

    /**
     * Products constructor.
     * @param $id
     * @param $name
     * @param $categoryId
     * @param $categoryName
     * @param $productId
     * @param $supplierPrice
     * @param $supplierId
     * @param $supplierCode1
     * @param $prevPrice
     * @param $supplierCode2
     * @param $prc
     */
    public function __construct($id, $name, $categoryId, $categoryName, $productId, $supplierPrice, $supplierId, $supplierCode1, $prevPrice, $supplierCode2, $prc)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->productId = $productId;
        $this->supplierPrice = $supplierPrice;
        $this->supplierId = $supplierId;
        $this->supplierCode1 = $supplierCode1;
        $this->prevPrice = $prevPrice;
        $this->supplierCode2 = $supplierCode2;
        $this->prc = $prc;
    }
}
<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SupplierCategory
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $syncCategoryId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isHidden;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;


    public function __construct($id, $pid, $supplierId, $name, $syncCategoryId, $isLeaf, $isHidden, $countProducts)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->supplierId = $supplierId;
        $this->name = $name;
        $this->syncCategoryId = $syncCategoryId;
        $this->isLeaf = $isLeaf;
        $this->isHidden = $isHidden;
        $this->countProducts = $countProducts;
    }
}
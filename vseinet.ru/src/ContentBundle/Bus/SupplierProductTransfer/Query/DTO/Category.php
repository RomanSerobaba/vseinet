<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Category
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
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $basename;

    /**
     * @Assert\Type(type="string")
     */
    public $gender;

    /**
     * @Assert\Type(type="integer")
     */
    public $aliasForId;

    /**
     * @Assert\Type(type="string")
     */
    public $linkedCategoryName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;


    public function __construct($id, $pid, $name, $basename, $gender, $aliasForId, $linkedCategoryName, $isLeaf, $countProducts)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->name = $name;
        $this->basename = $basename;
        $this->gender = $gender;
        $this->aliasForId = $aliasForId;
        $this->linkedCategoryName = $linkedCategoryName;
        $this->isLeaf = boolval($isLeaf);
        $this->countProducts = intval($countProducts);
    }
}
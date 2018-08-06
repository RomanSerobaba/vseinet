<?php 

namespace SupplyBundle\Bus\LowCostPurchases\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Categories
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
     * @Assert\Type(type="boolean")
     */
    public $isWarning;

    /**
     * @Assert\Type(type="integer")
     */
    public $count;

    /**
     * @Assert\Type(type="integer")
     */
    public $maxPrc;

    /**
     * @Assert\Type(type="array<namespace SupplyBundle\Bus\LowCostPurchases\Query\DTO\Categories>")
     */
    public $childrens;

    /**
     * Categories constructor.
     * @param $id
     * @param $name
     * @param $isWarning
     * @param $count
     * @param $maxPrc
     * @param $childrens
     */
    public function __construct($id, $name, $isWarning, $count=NULL, $maxPrc=NULL, $childrens=NULL)
    {
        $this->id = $id;
        $this->name = $name;
        $this->isWarning = $isWarning;
        $this->count = $count;
        $this->maxPrc = $maxPrc;
        $this->childrens = $childrens;
    }
}
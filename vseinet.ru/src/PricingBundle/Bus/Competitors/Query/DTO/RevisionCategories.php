<?php 

namespace PricingBundle\Bus\Competitors\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RevisionCategories
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
    public $pid;

    /**
     * @Assert\Type(type="array")
     */
    public $productsIds;

    /**
     * @Assert\Type(type="array")
     */
    public $children;

    /**
     * RevisionCategories constructor.
     *
     * @param $id
     * @param $name
     * @param $pid
     * @param $productsIds
     * @param $children
     */
    public function __construct($id = 0, $name = '', $pid = 0, $productsIds = [], $children = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
        $this->productsIds = $productsIds;
        $this->children = $children;
    }
}
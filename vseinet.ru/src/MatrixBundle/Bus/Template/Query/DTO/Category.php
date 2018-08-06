<?php 

namespace MatrixBundle\Bus\Template\Query\DTO;

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
    public $pid;

    /**
     * @Assert\Type(type="array")
     */
    public $childrenIds;

    /**
     * @Assert\Type(type="array")
     */
    public $productsIds;

    public function __construct($id, $name, $pid = null, $childrenIds = [], $productsIds = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
        $this->childrenIds = $childrenIds;
        $this->productsIds = $productsIds;
    }
}
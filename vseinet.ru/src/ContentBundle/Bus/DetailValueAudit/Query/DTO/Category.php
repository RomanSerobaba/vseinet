<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query\DTO;

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
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $categoryIds = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $detailIds = [];


    public function __construct($id, $name, $pid = null, $isLeaf = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
        $this->isLeaf = $isLeaf;
    }
}
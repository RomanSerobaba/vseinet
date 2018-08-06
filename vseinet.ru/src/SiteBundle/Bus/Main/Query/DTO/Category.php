<?php

namespace SiteBundle\Bus\Main\Query\DTO;

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
     * @Assert\Type(type="integer")
     */
    public $level;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $children = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $last = [];

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAll = false;

    /**
     * @Assert\Type(type="SiteBundle\Bus\Main\Query\DTO\Product")
     */
    public $product;


    public function __construct($id, $pid, $name, $level)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->name = $name;
        $this->level =  $level;
    }
}
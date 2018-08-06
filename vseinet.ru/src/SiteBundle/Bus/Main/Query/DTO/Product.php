<?php 

namespace SiteBundle\Bus\Main\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Product
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
    public $price;

    /**
     * @Assert\Type(type="string")
     */
    public $baseSrc = false;


    public function __construct($id, $name, $categoryId, $categoryName, $price, $baseSrc)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->price =  $price;
        $this->baseSrc = $baseSrc;
    }
}

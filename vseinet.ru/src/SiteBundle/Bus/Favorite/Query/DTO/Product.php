<?php 

namespace SiteBundle\Bus\Favorite\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    /**
     * @Assert\Type('type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="string")
     */
    public $baseSrc;


    public function __construct($id, $name, $price, $baseSrc)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->baseSrc = $baseSrc;
    }
}

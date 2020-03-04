<?php

namespace AppBundle\Bus\BurningOffers\Query\DTO;

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
     * @Assert\Type(type="string")
     */
    public $baseSrc;

    /**
     * @Assert\Type(type="string")
     */
    public $sefUrl;

    /**
     * @Assert\Type(type="string")
     */
    public $shortDescription;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAvailable;


    public function __construct($id, $name, $baseSrc, $price, $sefUrl = null, $isAvailable = false, $shortDescription = null) {
        $this->id = $id;
        $this->name = $name;
        $this->baseSrc = $baseSrc;
        $this->price = $price;
        $this->sefUrl = $sefUrl;
        $this->sefshortDescriptionUrl = $shortDescription;
        $this->isAvailable = (bool) $isAvailable;
    }
}

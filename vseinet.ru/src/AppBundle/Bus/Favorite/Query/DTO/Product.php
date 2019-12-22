<?php

namespace AppBundle\Bus\Favorite\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

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

    /**
     * @Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availabilityCode;

    /**
     * @Assert\DateTime
     */
    public $updatedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $sefUrl;

    public function __construct($id, $name, $price, $baseSrc, $availabilityCode, $updatedAt, $sefUrl = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->baseSrc = $baseSrc;
        $this->availabilityCode = $availabilityCode;
        $this->updatedAt = $updatedAt;
        $this->sefUrl = $sefUrl;
    }
}

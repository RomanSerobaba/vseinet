<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints AS Assert;
use AppBundle\Validator\Constraints\Enum;

class Features
{
    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Range")
     */
    public $price;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Finder\DTO\Category")
     */
    public $categories;

    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Brand")
     */
    public $brands;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Finder\DTO\CategorySection")
     */
    public $categorySections;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Finder\DTO\Detail")
     */
    public $details;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Finder\DTO\DetailValue")
     */
    public $detailValues;

    /**
     * @Assert\All(@Enum("AppBundle\Bus\Catalog\Enum\Availability"))
     */
    public $availability;

    /**
     * @Assert\All( @Enum("AppBundle\Bus\Catalog\Enum\Nofilled"))
     */
    public $nofilled;
}

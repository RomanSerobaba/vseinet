<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Facets
{
    /**
     * @Assert\type(type="integer")
     */
    public $total;

    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Range")
     */
    public $price;

    /**
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $categoryIds;

    /**
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $brandIds;

    /**
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $categorySectionIds;

    /**
     * @Assert\All(@Assert\Type(type="array"))
     */
    public $details;

    /**
     * @Assert\All(@Enum("AppBundle\Bus\Catalog\Enum\Availability"))
     */
    public $availability;
    /**
     * @Assert\All(@Enum("AppBundle\Bus\Catalog\Enum\Nofilled"))
     */
    public $nofilled;
}

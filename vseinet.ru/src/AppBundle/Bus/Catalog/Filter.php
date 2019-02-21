<?php

namespace AppBundle\Bus\Catalog;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Bus\Catalog\Enum\{ Availability, Sort, SortDirection };

class Filter
{
    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Query\DTO\Filter\Range")
     */
    public $price;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $brandIds;

    /**
     * @Assert\TYpe(type="array<integer>")
     */
    public $categoryIds;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $sectionIds;

    /**
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VEnum("AppBundle\Bus\Catalog\Enum\Availability")
     */
    public $availability = Availability::ACTIVE;

    /**
     * @Assert\All({
     *     @VIC\Enum("AppBundle\Bus\Catalog\Enum\Nofilled")
     * })
     */
    public $nofilled;

    /**
     * @Assert\Type(type="integer")
     */
    public $page;

    /**
     * @VIC\Enum("AppBundle\Bus\Catalog\Enum\Sort")
     */
    public $sort = Sort::DEFAULT;

    /**
     * @VIC\Enum("AppBundle\Bus\Catalog\Enum\SortDirection")
     */
    public $sortDirection = SortDirection::ASC;

    /**
     * @Assert\Type(type="array")
     */
    public $details = [];

}

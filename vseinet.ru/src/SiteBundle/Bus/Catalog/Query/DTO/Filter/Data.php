<?php 

namespace SiteBundle\Bus\Catalog\Query\DTO\Filter;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use SiteBundle\Bus\Catalog\Enum\Availability;
use SiteBundle\Bus\Catalog\Enum\Sort;
use SiteBundle\Bus\Catalog\Enum\SortDirection;

class Data
{
    /**
     * @Assert\Type(type="SiteBundle\Bus\Catalog\Query\DTO\Filter\Range")
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
     * @VIC\Enum("SiteBundle\Bus\Catalog\Enum\Availability")
     */
    public $availability = Availability::ACTIVE;

    /**
     * @Assert\All({
     *     @VIC\Enum("SiteBundle\Bus\Catalog\Enum\Nofilled")
     * })
     */
    public $nofilled; 

    /**
     * @Assert\Type(type="integer")
     */
    public $page;

    /**
     * @VIC\Enum("SiteBundle\Bus\Catalog\Enum\Sort")
     */
    public $sort = Sort::DEFAULT;

    /**
     * @VIC\Enum("SiteBundle\Bus\Catalog\Enum\SortDirection")
     */
    public $sortDirection = SortDirection::ASC;

    /**
     * @Assert\Type(type="array")
     */
    public $details = [];
}

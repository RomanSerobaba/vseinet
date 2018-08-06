<?php 

namespace SiteBundle\Bus\Catalog\Query\DTO\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class Facets
{
    /**
     * @Assert\Type(type="integer")
     */
    public $total;
    
    /**
     * @Assert\Type(type="SiteBundle\Bus\Catalog\Query\DTO\Filter\Range")
     */
    public $price;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $brandIds;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $sectionIds;

    /**
     * @Assert\Type(type="array")
     * @var [type]
     */
    public $details;
}

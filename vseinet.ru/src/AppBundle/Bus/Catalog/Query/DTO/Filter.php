<?php 

namespace AppBundle\Bus\Catalog\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class Filter
{
    /**
     * @Assert\Type(type="integer")
     */
    public $total;
    
    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Query\DTO\FIlter\Range")
     */
    public $price;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Query\DTO\Filter\Category")
     */
    public $categories;

    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Query\DTO\Filter\Brand")
     */
    public $brands;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Query\DTO\Filter\CategorySection")
     */
    public $sections;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Query\DTO\Filter\Detail")
     */
    public $details;

    /**
     * @Assert\Type(type="AppBUndle\Bus\Catalog\Query\DTO\Filter\DetailValue")
     */
    public $values;

    /**
     * @Assert\All({
     *     @VIC\Enum("AppBundle\Bus\Catalog\Enum\Availability")
     * })
     */
    public $availability;

    /**
     * @Assert\All({
     *     @VIC\Enum("AppBundle\Bus\Catalog\Enum\Nofilled")
     * })
     */
    public $nofilled;
}

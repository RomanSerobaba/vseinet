<?php 

namespace PricingBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @Assert\Type(type="integer")
     */
    public $retailPrice;
    
    public function __construct($id, $baseProductId, $cityId, $retailPrice)
    {
        $this->id = $id;
        $this->baseProductId = $baseProductId;
        $this->cityId = $cityId;
        $this->retailPrice = $retailPrice;
    }
}
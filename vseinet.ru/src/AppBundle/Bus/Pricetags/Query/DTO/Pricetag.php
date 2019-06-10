<?php

namespace AppBundle\Bus\Pricetags\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Pricetag
{
    /**
     * @Assert\Type("integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type("integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type("integer")
     */
    public $price;

    /**
     * @Assert\Type("boolean")
     */
    public $isHandmade;

    /**
     * @Enum("AppBundle\Enum\ProductPricetagSize")
     */
    public $size;

    /**
     * @Enum("AppBundle\Enum\ProductPricetagColor")
     */
    public $color;

    public function __construct($baseProductId, $geoPointId, $price, $isHandmade, $size, $color)
    {
        $this->baseProductId = $baseProductId;
        $this->geoPointId = $geoPointId;
        $this->price = $price;
        $this->isHandmade = $isHandmade;
        $this->size = $size;
        $this->color = $color;
    }
}

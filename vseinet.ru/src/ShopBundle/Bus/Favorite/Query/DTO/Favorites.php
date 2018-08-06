<?php 

namespace ShopBundle\Bus\Favorite\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Favorites
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="string")
     */
    public $brandLogo;

    /**
     * @Assert\Type(type="integer")
     */
    public $brandId;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointQuantity;
}
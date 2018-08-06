<?php 

namespace OrderBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ReservePoints
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCity;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPointCode;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPoint;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isInTransit;
}
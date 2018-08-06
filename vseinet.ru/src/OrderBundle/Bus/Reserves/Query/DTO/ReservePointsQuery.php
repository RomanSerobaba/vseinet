<?php 

namespace OrderBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ReservePointsQuery
{
    /**
     * @Assert\Type(type="integer")
     */
    public $pointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $shopQuantity;

    /**
     * @Assert\Type(type="string")
     */
    public $city;

    /**
     * @Assert\Type(type="string")
     */
    public $pointCode;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isInTransit;

    /**
     * @Assert\Type(type="integer")
     */
    public $reservedQuantity;
}
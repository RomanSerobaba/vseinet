<?php

namespace SupplyBundle\Bus\Orders\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Reserve
{
    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isInTransit;

    /**
     * Reserve constructor.
     *
     * @param $quantity
     * @param $geoPointId
     * @param $isInTransit
     */
    public function __construct($quantity, $geoPointId, $isInTransit)
    {
        $this->quantity = $quantity;
        $this->geoPointId = $geoPointId;
        $this->isInTransit = $isInTransit;
    }
}
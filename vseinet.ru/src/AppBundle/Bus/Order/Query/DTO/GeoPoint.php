<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Bus\User\Query\DTO\Contact;
use AppBundle\Enum\OrderItemStatus;

class GeoPoint
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasRetail;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasDelivery;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasRising;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    public function __construct($id, $name, $address, $hasRetail, $hasDelivery, $hasRising, $geoCityId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->hasRetail = $hasRetail;
        $this->hasDelivery = $hasDelivery;
        $this->hasRising = $hasRising;
        $this->geoCityId = $geoCityId;
    }
}

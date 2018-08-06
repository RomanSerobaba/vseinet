<?php 

namespace SupplyBundle\Bus\Shipment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Supplier
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="integer")
     */
    public $unformedQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $formingQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $transitQuantity;


    /**
     * Supplier constructor.
     *
     * @param $id
     * @param $code
     * @param $unformedQuantity
     * @param $formingQuantity
     * @param $transitQuantity
     */
    public function __construct($id, $code, $unformedQuantity, $formingQuantity, $transitQuantity)
    {
        $this->id = $id;
        $this->code = $code;
        $this->unformedQuantity = $unformedQuantity;
        $this->formingQuantity = $formingQuantity;
        $this->transitQuantity = $transitQuantity;
    }
}
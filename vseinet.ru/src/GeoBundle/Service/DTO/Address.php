<?php 

namespace GeoBundle\Service\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $street;

    /**
     * @Assert\Type(type="string")
     */
    public $unit;

    /**
     * @Assert\Type(type="string")
     */
    public $house;

    /**
     * @Assert\Type(type="string")
     */
    public $building;

    /**
     * @Assert\Type(type="string")
     */
    public $apartment;

    /**
     * @Assert\Type(type="string")
     */
    public $address;


    public function __construct($id = null, $street = null, $unit = null, $house = null, $building = null, $apartment = null)
    {
        $this->id = $id;
        $this->street = $street;
        $this->unit = $unit;
        $this->house = $house;
        $this->building = $building;
        $this->apartment = $apartment;
    }

    public function format(): self 
    {
        $this->address = $this->street;
        if ($this->unit) {
            $this->address .= ' '.$this->unit;
        }
        if ($this->house) {
            $this->address .= ', '.$this->house;
        }

        return $this;
    }
}
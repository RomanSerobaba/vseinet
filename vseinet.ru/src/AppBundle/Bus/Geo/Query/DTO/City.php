<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class City
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
     * @Assert\Type(type="boolean")
     */
    public $isCentral;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasRetail = false;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasDelivery = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $countNewPoints = 0;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCurrent = false;


    public function __construct($id, $name, $isCentral)
    {
        $this->id = $id;
        $this->name = $name;
        $this->isCentral = $isCentral;
    }
}
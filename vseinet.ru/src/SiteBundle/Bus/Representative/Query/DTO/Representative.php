<?php 

namespace SiteBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Representative
{
    /**
     * @Assert\Type(type="integer")
     */
    public $regionId;

    /**
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @Assert\Type(type="string")
     */
    public $cityName;

    /**
     * @Assert\Type(type="integer")
     */
    public $pointId;

    /**
     * @Assert\Type(type="string")
     */
    public $pointName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCentral;

    /**
     * @Assert\Type(type="integer")
     */
    public $addressId;

    /**
     * @Assert\Type(type="GeoBundle\Service\DTO\Address")
     */
    public $address;

    /**
     * @Assert\Type(type="array<string>")
     */
    public $contacts = [];

    /**
     * @Assert\Type(type="SiteBundle\Bus\Representative\Query\DTO\Schedule")
     */
    public $schedule;

    /**
     * @Assert\Type(type="integer")
     */
    public $countPoints = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $countNewPoints = 0;

    /**
     * @Assert\Type(type="array<SiteBundle\Bus\Representaive\Query\DTO\Representative>")
     */


    public function __construct($regionId, $cityId, $cityName, $pointId, $pointName, $isCentral, $addressId)
    {
        $this->regionId = $regionId;
        $this->cityId = $cityId;
        $this->cityName = $cityName;
        $this->pointId = $pointId;
        $this->pointName = $pointName;
        $this->isCentral = $isCentral;
        $this->addressId = $addressId;
    }
}
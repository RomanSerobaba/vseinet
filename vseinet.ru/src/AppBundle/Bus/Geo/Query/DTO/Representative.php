<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Representative
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoRegionId;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCentral;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoAddressId;

    /**
     * @Assert\Type(type="AppBundle\Entity\GeoAddress")
     */
    public $geoAddress;

    /**
     * @Assert\Type(type="array<string>")
     */
    public $contacts = [];

    /**
     * @Assert\Type(type="AppBundle\Bus\Geo\Query\DTO\Schedule")
     */
    public $schedule;


    public function __construct($geoRegionId, $geoCityId, $geoCityName, $geoPointId, $geoPointName, $isCentral, $geoAddressId)
    {
        $this->geoRegionId = $geoRegionId;
        $this->geoCityId = $geoCityId;
        $this->geoCityName = $geoCityName;
        $this->geoPointId = $geoPointId;
        $this->geoPointName = $geoPointName;
        $this->isCentral = $isCentral;
        $this->geoAddressId = $geoAddressId;
    }
}

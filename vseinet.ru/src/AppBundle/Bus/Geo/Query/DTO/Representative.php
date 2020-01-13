<?php

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Representative
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasRetail;

    /**
     * @Assert\Type(type="array<AppBundle\Entity\RepresentativePhoto>")
     */
    public $photos;

    /**
     * @Assert\Type(type="array<string>")
     */
    public $phones;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Geo\Query\DTO\ScheduleItem>")
     */
    public $schedule;


    public function __construct(
        $geoPointId,
        $geoPointName,
        $geoCityName,
        $address,
        $hasRetail
    ) {
        $this->geoPointId = $geoPointId;
        $this->geoPointName = $geoPointName;
        $this->geoCityName = $geoCityName;
        $this->address = $address;
        $this->hasRetail = $hasRetail;
    }
}

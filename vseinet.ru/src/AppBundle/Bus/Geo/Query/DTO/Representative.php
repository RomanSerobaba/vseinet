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
     * @Assert\Type(type="boolean")
     */
    public $hasDelivery;

    /**
     * @Assert\Type(type="float")
     */
    public $longitude;

    /**
     * @Assert\Type(type="float")
     */
    public $latitude;

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

    /**
     * @Assert\Type(type="string")
     */
    public $fullSchedule;
}

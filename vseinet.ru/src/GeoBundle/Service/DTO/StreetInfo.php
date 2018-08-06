<?php

namespace GeoBundle\Service\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StreetInfo extends CityInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $streetId;

    /**
     * @Assert\Type(type="string")
     */
    public $streetName;

    /**
     * @Assert\Type(type="string")
     */
    public $streetUnit;

    /**
     * @Assert\Type(type="string")
     */
    public $streetAoguid;

    /**
     * StreetInfo constructor.
     * @param $regiontId
     * @param $regionName
     * @param $regionUnit
     * @param $regionAoguid
     * @param $cityId
     * @param $cityName
     * @param $cityUnit
     * @param $cityIsCentral
     * @param $cityPhoneCode
     * @param $cityAoguid
     * @param $streetId
     * @param $streetName
     * @param $streetUnit
     * @param null $streetAoguid
     */
    public function __construct(
        $regiontId,
        $regionName,
        $regionUnit,
        $regionAoguid,
        $cityId,
        $cityName,
        $cityUnit,
        $cityIsCentral,
        $cityPhoneCode,
        $cityAoguid,
        $streetId,
        $streetName,
        $streetUnit,
        $streetAoguid=null
    )
    {
        parent::__construct($regiontId, $regionName, $regionUnit, $regionAoguid,
            $cityId, $cityName, $cityUnit, $cityIsCentral, $cityPhoneCode, $cityAoguid);

        $this->streetId = $streetId;
        $this->streetName = $streetName;
        $this->streetUnit = $streetUnit;
        $this->streetAoguid = $streetAoguid;
    }
}
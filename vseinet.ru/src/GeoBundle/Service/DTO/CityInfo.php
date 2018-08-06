<?php

namespace GeoBundle\Service\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CityInfo extends RegionInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @Assert\Type(type="string")
     */
    public $cityName;

    /**
     * @Assert\Type(type="string")
     */
    public $cityUnit;

    /**
     * @Assert\Type(type="boolean")
     */
    public $cityIsCentral;

    /**
     * @Assert\Type(type="string")
     */
    public $cityPhoneCode;

    /**
     * @Assert\Type(type="string")
     */
    public $cityAoguid;

    /**
     * CityInfo constructor.
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
        $cityAoguid=null
    )
    {
        parent::__construct($regiontId, $regionName, $regionUnit, $regionAoguid);

        $this->cityId = $cityId;
        $this->cityName = $cityName;
        $this->cityUnit = $cityUnit;
        $this->cityIsCentral = $cityIsCentral;
        $this->cityPhoneCode = $cityPhoneCode;
        $this->cityAoguid = $cityAoguid;
    }
}
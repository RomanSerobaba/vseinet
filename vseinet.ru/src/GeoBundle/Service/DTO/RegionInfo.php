<?php

namespace GeoBundle\Service\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegionInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $regiontId;

    /**
     * @Assert\Type(type="string")
     */
    public $regionName;

    /**
     * @Assert\Type(type="string")
     */
    public $regionUnit;

    /**
     * @Assert\Type(type="string")
     */
    public $regionAoguid;

    /**
     * CityInfo constructor.
     * @param $regiontId
     * @param $regionName
     * @param $regionUnit
     * @param $regionAoguid
     */
    public function __construct(
        $regiontId,
        $regionName,
        $regionUnit,
        $regionAoguid=null
    )
    {
        $this->regiontId = $regiontId;
        $this->regionName = $regionName;
        $this->regionUnit = $regionUnit;
        $this->regionAoguid = $regionAoguid;
    }
}
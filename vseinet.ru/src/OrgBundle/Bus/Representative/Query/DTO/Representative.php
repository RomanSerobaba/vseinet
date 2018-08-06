<?php

namespace OrgBundle\Bus\Representative\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class Representative
{
    /**
     * @VIA\Description("Geo point id")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Department id")
     * @Assert\Type(type="integer")
     */
    public $departmentId;

    /**
     * @VIA\Description("Geo point code")
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @VIA\Description("Geo point name")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Geo city id")
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @VIA\Description("Geo street id")
     * @Assert\Type(type="integer")
     */
    public $streetId;

    /**
     * @Assert\Type(type="string")
     */
    public $house;

    /**
     * @Assert\Type(type="string")
     */
    public $building;

    /**
     * @Assert\Type(type="integer")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCentral;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isPartner;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="string")
     */
    public $phone1;

    /**
     * @Assert\Type(type="string")
     */
    public $phone2;

    /**
     * @Assert\Type(type="string")
     */
    public $phone3;

    /**
     * @Assert\Type(type="string")
     */
    public $since1;
    /**
     * @Assert\Type(type="string")
     */
    public $till1;
    /**
     * @Assert\Type(type="string")
     */
    public $since2;
    /**
     * @Assert\Type(type="string")
     */
    public $till2;
    /**
     * @Assert\Type(type="string")
     */
    public $since3;
    /**
     * @Assert\Type(type="string")
     */
    public $till3;
    /**
     * @Assert\Type(type="string")
     */
    public $since4;
    /**
     * @Assert\Type(type="string")
     */
    public $till4;
    /**
     * @Assert\Type(type="string")
     */
    public $since5;
    /**
     * @Assert\Type(type="string")
     */
    public $till5;
    /**
     * @Assert\Type(type="string")
     */
    public $since6;
    /**
     * @Assert\Type(type="string")
     */
    public $till6;
    /**
     * @Assert\Type(type="string")
     */
    public $since7;
    /**
     * @Assert\Type(type="string")
     */
    public $till7;

    /**
     * @var \GeoBundle\Bus\Cities\Query\DTO\CityInfo
     *
     * @Assert\Type(type="GeoBundle\Bus\Cities\Query\DTO\CityInfo")
     */
    public $cityInfo;

    /**
     * @var \GeoBundle\Service\DTO\StreetInfo
     *
     * @Assert\Type(type="GeoBundle\Service\DTO\StreetInfo")
     */
    public $streetInfo;

    /**
     * Representative constructor.
     * @param $id
     * @param $departmentId
     * @param $code
     * @param $name
     * @param $cityId
     * @param $streetId
     * @param $house
     * @param $building
     * @param $floor
     * @param $isCentral
     * @param $isPartner
     * @param $isActive
     * @param $phone1
     * @param $phone2
     * @param $phone3
     * @param $since1
     * @param $till1
     * @param $since2
     * @param $till2
     * @param $since3
     * @param $till3
     * @param $since4
     * @param $till4
     * @param $since5
     * @param $till5
     * @param $since6
     * @param $till6
     * @param $since7
     * @param $till7
     * @param $cityInfo
     * @param $streetInfo
     */
    public function __construct(
        $id,
        $departmentId,
        $code,
        $name,
        $cityId,
        $streetId,
        $house,
        $building,
        $floor,
        $isCentral,
        $isPartner,
        $isActive,
        $phone1=null,
        $phone2=null,
        $phone3=null,
        $since1=null,
        $till1=null,
        $since2=null,
        $till2=null,
        $since3=null,
        $till3=null,
        $since4=null,
        $till4=null,
        $since5=null,
        $till5=null,
        $since6=null,
        $till6=null,
        $since7=null,
        $till7=null,
        $cityInfo=null,
        $streetInfo=null
    )
    {
        $this->id = $id;
        $this->departmentId = $departmentId;
        $this->code = $code;
        $this->name = $name;
        $this->cityId = $cityId;
        $this->streetId = $streetId;
        $this->house = $house;
        $this->building = $building;
        $this->floor = $floor;
        $this->phone1 = $phone1;
        $this->phone2 = $phone2;
        $this->phone3 = $phone3;
        $this->isCentral = $isCentral;
        $this->isPartner = $isPartner;
        $this->isActive = $isActive;
        $this->since1 = $since1;
        $this->till1 = $till1;
        $this->since2 = $since2;
        $this->till2 = $till2;
        $this->since3 = $since3;
        $this->till3 = $till3;
        $this->since4 = $since4;
        $this->till4 = $till4;
        $this->since5 = $since5;
        $this->till5 = $till5;
        $this->since6 = $since6;
        $this->till6 = $till6;
        $this->since7 = $since7;
        $this->till7 = $till7;
        $this->cityInfo = $cityInfo;
        $this->streetInfo = $streetInfo;
    }
}
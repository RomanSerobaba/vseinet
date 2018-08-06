<?php

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateRepresentativeShortCommand extends Message
{
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
     * @Assert\Uuid
     */
    public $uuid;
}
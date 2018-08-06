<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoRoomInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAccountable;

    /**
     * @Assert\Type(type="string")
     */
    public $roomType;

    /**
     * @Assert\Type(type="string")
     */
    public $roomName;

    /**
     * @Assert\Type(type="string")
     */
    public $roomCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="string")
     */
    public $pointType;

    /**
     * @Assert\Type(type="string")
     */
    public $pointName;

    /**
     * @Assert\Type(type="string")
     */
    public $pointCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoAddressId;

    /**
     * @var \AppBundle\Bus\User\Query\DTO\Address|null
     *
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\Address")
     */
    public $address;

    /**
     * GeoRoomInfo constructor.
     * @param $geoRoomId
     * @param $isMain
     * @param $isAccountable
     * @param $roomType
     * @param $roomName
     * @param $roomCode
     * @param $geoPointId
     * @param $pointType
     * @param $pointName
     * @param $pointCode
     * @param $geoAddressId
     * @param $address
     */
    public function __construct(
        $geoRoomId,
        $isMain,
        $isAccountable,
        $roomType,
        $roomName,
        $roomCode,
        $geoPointId,
        $pointType,
        $pointName,
        $pointCode,
        $geoAddressId,
        $address=null
    )
    {
        $this->geoRoomId = $geoRoomId;
        $this->isMain = $isMain;
        $this->isAccountable = $isAccountable;
        $this->roomType = $roomType;
        $this->roomName = $roomName;
        $this->roomCode = $roomCode;
        $this->geoPointId = $geoPointId;
        $this->pointType = $pointType;
        $this->pointName = $pointName;
        $this->pointCode = $pointCode;
        $this->geoAddressId = $geoAddressId;
        $this->address = $address;
    }
}

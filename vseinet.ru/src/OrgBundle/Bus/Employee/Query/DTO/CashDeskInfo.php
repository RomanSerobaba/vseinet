<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CashDeskInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $cashDeskId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isDefault;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="integer")
     */
    public $departmentId;

    /**
     * @Assert\Type(type="string")
     */
    public $departmentNumber;

    /**
     * @Assert\Type(type="string")
     */
    public $departmentName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoRoomType;

    /**
     * @Assert\Type(type="string")
     */
    public $geoRoomName;

    /**
     * CashDeskInfo constructor.
     * @param $cashDeskId
     * @param $isDefault
     * @param $title
     * @param $departmentId
     * @param $departmentNumber
     * @param $departmentName
     * @param $geoRoomId
     * @param $geoRoomType
     * @param $geoRoomName
     */
    public function __construct(
        $cashDeskId,
        $isDefault,
        $title,
        $departmentId,
        $departmentNumber,
        $departmentName,
        $geoRoomId,
        $geoRoomType,
        $geoRoomName
    )
    {
        $this->cashDeskId = $cashDeskId;
        $this->isDefault = $isDefault;
        $this->title = $title;
        $this->departmentId = $departmentId;
        $this->departmentNumber = $departmentNumber;
        $this->departmentName = $departmentName;
        $this->geoRoomId = $geoRoomId;
        $this->geoRoomType = $geoRoomType;
        $this->geoRoomName = $geoRoomName;
    }
}
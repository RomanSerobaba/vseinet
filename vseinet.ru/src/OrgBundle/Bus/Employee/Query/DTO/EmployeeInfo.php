<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeeInfo
{
    /**
     * Идентификатор сотрудника
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * Идентификатор подразделения
     * @Assert\Type(type="integer")
     */
    public $departmentId;

    /**
     * Номер подразделения
     * @Assert\Type(type="string")
     */
    public $departmentNumber;

    /**
     * Название подразделения
     * @Assert\Type(type="string")
     */
    public $departmentName;

    /**
     * Ф.И.О. сотрудника
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $sortOrder;

    /**
     * @Assert\Type(type="string")
     */
    public $position;

    /**
     * @var \DateTime|null
     * @Assert\Type(type="DateTime")
     */
    public $hiredAt;

    /**
     * @var \DateTime|null
     * @Assert\Type(type="DateTime")
     */
    public $firedAt;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="integer")
     */
    public $workingHoursWeekly;

    /**
     * @var \DateTime|null
     * @Assert\Type(type="DateTime")
     */
    public $onWorkAt;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isIrregular;

    /**
     * @var \DateTime|null
     * @Assert\Type(type="DateTime")
     */
    public $workSince;

    /**
     * @var \DateTime|null
     * @Assert\Type(type="DateTime")
     */
    public $workTill;

    /**
     * @var SubroleInfo[]|null
     * @Assert\All({
     *     @Assert\Type(type="OrgBundle\Bus\Employee\Query\DTO\SubroleInfo")
     * })
     */
    public $subroles;

    /**
     * @var GeoRoomInfo|null
     * @Assert\Type(type="OrgBundle\Bus\Employee\Query\DTO\GeoRoomInfo")
     */
    public $geoRoom;

    /**
     * @var ContactInfo|null
     * @Assert\Type(type="OrgBundle\Bus\Employee\Query\DTO\ContactInfo")
     */
    public $contact;

    /**
     * @var CashDeskInfo[]|null
     * @Assert\All({
     *     @Assert\Type(type="OrgBundle\Bus\Employee\Query\DTO\CashDeskInfo")
     * })
     */
    public $cashDesks;


    /**
     * Employee constructor.
     * @param $userId
     * @param $departmentId
     * @param $departmentNumber
     * @param $departmentName
     * @param $name
     * @param $sortOrder
     * @param $position
     * @param $hiredAt
     * @param $firedAt
     * @param $isActive
     * @param $workingHoursWeekly
     * @param $onWorkAt
     * @param bool $isIrregular
     * @param $workSince
     * @param $workTill
     * @param $subroles
     * @param $geoRoom
     * @param $contact
     * @param $cashDesks
     */
    public function __construct(
        $userId,
        $departmentId,
        $departmentNumber,
        $departmentName,
        $name,
        $sortOrder,
        $position,
        $hiredAt,
        $firedAt,
        $isActive,
        $workingHoursWeekly=null,
        $onWorkAt=null,
        $isIrregular=null,
        $workSince=null,
        $workTill=null,
        $subroles=null,
        $geoRoom=null,
        $contact=null,
        $cashDesks=null
    )
    {
        $this->userId = $userId;
        $this->departmentId = $departmentId;
        $this->departmentNumber = $departmentNumber;
        $this->departmentName = $departmentName;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->position = $position;
        $this->hiredAt = $hiredAt;
        $this->firedAt = $firedAt;
        $this->isActive = $isActive;
        $this->workingHoursWeekly = $workingHoursWeekly;
        $this->onWorkAt = $onWorkAt;
        $this->isIrregular = $isIrregular;
        $this->workSince = $workSince;
        $this->workTill = $workTill;
        $this->subroles = $subroles;
        $this->geoRoom = $geoRoom;
        $this->contact = $contact;
        $this->cashDesks = $cashDesks;
    }
}
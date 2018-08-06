<?php

namespace OrgBundle\Bus\Contact\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactInfo
{
    /**
     * @Assert\Type(type="numeric")
     */
    public $contactId;

    /**
     * @Assert\Choice({"mobile", "phone", "email", "skype", "icq", "custom"})
     */
    public $contactType;

    /**
     * @Assert\Type(type="string")
     */
    public $contactValue;

    /**
     * @Assert\Type(type="numeric")
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
     * @Assert\Type(type="numeric")
     */
    public $employeetId;

    /**
     * @Assert\Type(type="string")
     */
    public $employeeName;

    /**
     * ContactInfo constructor.
     * @param $contactId
     * @param $contactType
     * @param $contactNumber
     * @param $departmentId
     * @param $departmentNumber
     * @param $departmentName
     * @param $employeetId
     * @param $employeeName
     */
    public function __construct(
        $contactId,
        $contactType,
        $contactNumber,
        $departmentId=null,
        $departmentNumber=null,
        $departmentName=null,
        $employeetId=null,
        $employeeName=null
    )
    {
        $this->contactId = $contactId;
        $this->contactType = $contactType;
        $this->contactValue = $contactNumber;
        $this->departmentId = $departmentId;
        $this->departmentNumber = $departmentNumber;
        $this->departmentName = $departmentName;
        $this->employeetId = $employeetId;
        $this->employeeName = $employeeName;
    }
}
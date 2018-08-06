<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $contactId;

    /**
     * @Assert\Type(type="integer")
     */
    public $departmentId;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    /**
     * ContactInfo constructor.
     * @param $contactId
     * @param $departmentId
     * @param $type
     * @param $value
     * @param $isMain
     */
    public function __construct(
        $contactId,
        $departmentId,
        $type,
        $value,
        $isMain
    )
    {
        $this->contactId = $contactId;
        $this->departmentId = $departmentId;
        $this->type = $type;
        $this->value = $value;
        $this->isMain = $isMain;
    }
}
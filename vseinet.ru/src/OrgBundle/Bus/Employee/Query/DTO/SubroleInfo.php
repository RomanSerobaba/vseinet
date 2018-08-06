<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SubroleInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $subroleId;

    /**
     * @Assert\Type(type="integer")
     */
    public $roleId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="integer")
     */
    public $sortOrder;

    /**
     * @Assert\Type(type="integer")
     */
    public $grade;

    /**
     * SubroleInfo constructor.
     * @param $subroleId
     * @param $roleId
     * @param $name
     * @param $code
     * @param $sortOrder
     * @param $grade
     */
    public function __construct(
        $subroleId,
        $roleId,
        $name,
        $code,
        $sortOrder,
        $grade
    )
    {
        $this->subroleId = $subroleId;
        $this->roleId = $roleId;
        $this->name = $name;
        $this->code = $code;
        $this->sortOrder = $sortOrder;
        $this->grade = $grade;
    }
}
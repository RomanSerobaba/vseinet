<?php 

namespace OrgBundle\Bus\DepartmentType\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDepartmentTypeActivityCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $departmentTypeId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $activityIndexId;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $activityObjectId;

    /**
     * @Assert\Type(type="integer")
     */
    public $activityAreaId;

    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer")
     */
    public $intervalMonth;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isChief;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isDepartment;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $coefficient;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isPlanned;

    /**
     * @Assert\Type(type="integer")
     */
    public $rate;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}

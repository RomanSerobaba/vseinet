<?php

namespace OrgBundle\Bus\DepartmentType\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteDepartmentTypeActivityCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Department type id id can't be blank")
     */
    public $departmentTypeId;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Activity id can't be blank")
     */
    public $activityId;
}
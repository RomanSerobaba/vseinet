<?php

namespace OrgBundle\Bus\DepartmentType\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetDepartmentTypeActivitiesQuery extends Message
{
    /**
     * @VIA\Description("DepartmentType id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(
     *     message="DepartmentType id should not be blank."
     * )
     */
    public $id;
}
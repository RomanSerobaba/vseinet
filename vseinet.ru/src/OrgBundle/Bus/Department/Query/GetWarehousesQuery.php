<?php

namespace OrgBundle\Bus\Department\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetWarehousesQuery extends Message
{
    /**
     * @VIA\Description("Department id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(
     *     message="Department id should not be blank."
     * )
     */
    public $id;
}
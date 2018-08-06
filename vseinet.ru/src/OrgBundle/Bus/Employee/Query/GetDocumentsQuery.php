<?php

namespace OrgBundle\Bus\Employee\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetDocumentsQuery extends Message
{
    /**
     * @VIA\Description("Employee id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(
     *     message="Employee id should not be blank."
     * )
     */
    public $id;
}
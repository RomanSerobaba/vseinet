<?php 

namespace OrgBundle\Bus\Complaint\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CheckComplaintCommand extends Message
{
    /**
     * @VIA\Description("Complaint id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Обработан")
     * @Assert\Type(type="boolean")
     */
    public $value;
}
<?php 

namespace OrgBundle\Bus\Complaint\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddCommentCommand extends Message
{
    /**
     * @VIA\Description("Complaint id")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Комментарий")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
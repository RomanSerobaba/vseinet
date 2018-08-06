<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddRoomCommand extends Message
{
    /**
     * @VIA\Description("Representative id")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Название")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $name;
}
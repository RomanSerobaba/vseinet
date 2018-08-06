<?php 

namespace DocumentBundle\Bus\Comment\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор комментария")
     * @Assert\NotBlank(message="Идентификатор комментария должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}
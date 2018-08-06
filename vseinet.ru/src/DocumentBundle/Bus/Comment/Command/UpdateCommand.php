<?php 

namespace DocumentBundle\Bus\Comment\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор комментария")
     * @Assert\NotBlank(message="Идентификатор комментария должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Содержание комментария")
     * @Assert\NotBlank(message="Содержание комментария должно быть указано")
     * @Assert\Type(type="string")
     */
    public $comment;

}

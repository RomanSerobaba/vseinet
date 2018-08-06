<?php 

namespace DocumentBundle\Bus\Comment\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    

    /**
     * @VIA\Description("Идентификатор претензии")
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $documentId;

    /**
     * @VIA\Description("Содержание комментария")
     * @Assert\Type(type="string")
     */
    public $comment;

    // Обратная связь
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}
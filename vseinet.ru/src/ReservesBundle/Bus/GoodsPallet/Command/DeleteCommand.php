<?php 

namespace ReservesBundle\Bus\GoodsPallet\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор паллеты")
     * @Assert\NotBlank(message="Идентификатор паллеты должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

}
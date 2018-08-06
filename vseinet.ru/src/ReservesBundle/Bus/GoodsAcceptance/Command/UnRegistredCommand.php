<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UnRegistredCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Уникальный идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

}
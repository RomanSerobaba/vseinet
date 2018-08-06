<?php 

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UnRegistrationCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

}
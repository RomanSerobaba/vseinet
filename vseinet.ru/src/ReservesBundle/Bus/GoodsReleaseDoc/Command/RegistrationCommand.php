<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class RegistrationCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Уникальнй идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

}
<?php 

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UnRegistrationCommand extends Message
{    
    /**
     * @VIA\Description("Уникальный идентификатор документа")
     * @Assert\NotBlank(message="Уникальный идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

}
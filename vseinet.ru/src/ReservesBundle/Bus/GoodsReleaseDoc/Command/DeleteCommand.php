<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Уникальный идентификатор документа")
     * @Assert\NotBlank(message="Уникальный идентификатор документа не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;
}
<?php 

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа комплектации/разкомплектации")
     * @Assert\NotBlank(message="Идентификатор документа комплектации/разкомплектации не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;
}
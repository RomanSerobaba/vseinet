<?php 

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CompletedCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Признак завершения/закрытия документа")
     * @Assert\Type(type="boolean")
     */
    public $completed;
    

}
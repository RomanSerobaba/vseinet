<?php 

namespace FinanseBundle\Bus\AccountableExpensesDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class StatusCommand extends Message
{    
    /**
     * @VIA\Description("Унивесрсальный идентификатор документа")
     * @Assert\NotBlank(message="Унивесрсальный идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Статус документа")
     * @Assert\NotBlank(message="Статус документа должен быть указан")
     * @Assert\Choice({"new", "active", "wait", "rejected", "completed"}, strict=true, multiple=false)
     */
    public $statusCode;
    

}
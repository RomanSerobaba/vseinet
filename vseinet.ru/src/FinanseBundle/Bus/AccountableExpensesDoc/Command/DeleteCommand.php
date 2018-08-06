<?php 

namespace FinanseBundle\Bus\AccountableExpensesDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}
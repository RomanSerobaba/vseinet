<?php 

namespace ReservesBundle\Bus\GoodsIssueDocType\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор строки документа")
     * @Assert\NotBlank(message="Идентификатор строки документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

}
<?php 

namespace ReservesBundle\Bus\InventoryProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ResetCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа инвентаризации")
     * 
     * @Assert\NotBlank(message="Идентификатор документа инвентаризации не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $inventoryId;

}
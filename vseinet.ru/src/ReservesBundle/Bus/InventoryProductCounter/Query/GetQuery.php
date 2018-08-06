<?php 

namespace ReservesBundle\Bus\InventoryProductCounter\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор инвентаризации")
     * @Assert\NotBlank(message="Идентификатор документа инвентаризации не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $inventoryId;
    
}
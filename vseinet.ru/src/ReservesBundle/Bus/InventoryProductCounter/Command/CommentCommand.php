<?php 

namespace ReservesBundle\Bus\InventoryProductCounter\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CommentCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа инвентаризации")
     * 
     * @Assert\NotBlank(message="Идентификатор документа инвентаризации не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $inventoryId;

    /**
     * @VIA\Description("Идентификатор товара")
     * 
     * @Assert\NotBlank(message="Идентификатор товара не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Комментарий к позиции товара")
     * 
     * @Assert\Type(type="string")
     */
    public $comment;

}
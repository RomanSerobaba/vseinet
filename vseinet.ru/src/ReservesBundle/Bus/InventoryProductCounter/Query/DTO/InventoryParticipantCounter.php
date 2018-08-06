<?php

namespace ReservesBundle\Bus\InventoryProductCounter\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class InventoryParticipantCounter
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор документа")
     */
    private $inventoryId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор участника")
     */
    private $participantId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование участника")
     */
    private $participantName;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор участника")
     */
    private $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование продукта")
     */
    private $name;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Количество продукта, подсчитанного участником")
     */
    private $foundQuantity;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Комментарий")
     */
    private $comment;

    public function __construct($inventoryId, $participantId, $participantName, $baseProductId, $baseProductName, $foundQuantity, $comment)
    {
        $this->inventoryId = $inventoryId;
        $this->participantId = $participantId;
        $this->participantName = $participantName;
        $this->id = $baseProductId;
        $this->name = $baseProductName;
        $this->foundQuantity = $foundQuantity;
        $this->comment = $comment;
    }
}
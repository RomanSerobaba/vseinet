<?php

namespace ReservesBundle\Bus\InventoryProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FoundQuantityByParticipants
{
    /**
     * @VIA\Description("Идентификатор участника")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Количество подсчитанного участником товара")
     * @Assert\Type(type="integer")
     */
    public $count;

    public function __construct($id, $count)
    {
        $this->id = $id;
        $this->count = $count;
    }
}
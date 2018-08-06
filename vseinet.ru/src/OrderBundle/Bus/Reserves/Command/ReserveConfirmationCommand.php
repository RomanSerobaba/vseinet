<?php

namespace OrderBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ReserveConfirmationCommand extends Message
{
    /**
     * @VIA\Description("Order item id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Quantities [['pointId' => 1, 'quantity' => 7, 'isInTransit' => false], ...]")
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $reservingQuantities;
}
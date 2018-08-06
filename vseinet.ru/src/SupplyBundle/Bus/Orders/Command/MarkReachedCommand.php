<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class MarkReachedCommand extends Message
{
    /**
     * @VIA\Description("Order id")
     * @Assert\NotBlank(message="Идентификатор заказа не должен быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Value")
     * @Assert\Type(type="boolean")
     */
    public $value;
}
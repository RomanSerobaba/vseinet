<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetIsActiveCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;
}
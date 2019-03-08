<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class SetPriceCommand extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\NotBlank
     * @Enum("AppBundle\Enum\ProductPriceType")
     */
    public $type;
}

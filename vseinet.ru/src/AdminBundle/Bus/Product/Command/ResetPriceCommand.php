<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPriceCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;
}

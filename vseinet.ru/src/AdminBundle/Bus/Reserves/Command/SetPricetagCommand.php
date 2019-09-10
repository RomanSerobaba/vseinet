<?php

namespace AdminBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetPricetagCommand extends Message
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
    public $geoPointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;
}

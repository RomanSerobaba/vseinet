<?php

namespace AppBundle\Bus\Pricetags\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ToggleCommand extends Message
{
    /**
     * @Assert\Type("integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type("integer")
     */
    public $geoPointId;
}

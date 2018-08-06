<?php

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ToggleIsActiveCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Конкурент")
     * @Assert\NotNull()
     */
    public $id;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Активен")
     */
    public $isActive;
}
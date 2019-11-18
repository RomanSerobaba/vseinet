<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddViewHistoryCategoryCommand extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $categoryId;
}

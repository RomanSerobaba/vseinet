<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetNotAvailableCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите товар партнера")
     * @Assert\Type(type="numeric")
     */
    public $partnerProductId;
}

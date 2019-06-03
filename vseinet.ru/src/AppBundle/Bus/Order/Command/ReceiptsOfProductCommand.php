<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ReceiptsOfProductCommand extends Message
{
    /**
     * @Assert\Type("integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type("AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;

    /**
     * @Assert\Type("integer")
     */
    public $trackingPeriod;
}

<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Укажите тип заказа")
     * @VIC\Enum("AppBundle\Enum\OrderType")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $id;
}

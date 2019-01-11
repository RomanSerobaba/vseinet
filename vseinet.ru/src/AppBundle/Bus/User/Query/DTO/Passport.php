<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Bus\User\Query\DTO\Contact;
use AppBundle\Enum\OrderItemStatus;

class Passport
{
    /**
     * @Assert\Type(type="string")
     */
    public $seria;

    /**
     * @Assert\Type(type="string")
     */
    public $number;

    /**
     * @Assert\Type(type="date")
     */
    public $issuedAt;
}

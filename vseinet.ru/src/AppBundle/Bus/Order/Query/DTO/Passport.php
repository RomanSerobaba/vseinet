<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\Type(type="datetime", message="Дата выдачи паспорта должна быть в формате ДД.ММ.ГГГГ")
     */
    public $issuedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $issuedBy;
}

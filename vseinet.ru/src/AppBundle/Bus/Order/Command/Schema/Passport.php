<?php

namespace AppBundle\Bus\Order\Command\Schema;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class Passport extends Message
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
     * @Assert\Date(message="Дата выдачи паспорта должна быть в формате ДД.ММ.ГГГГ")
     */
    public $issuedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $issuedBy;

    public function setIssuedAt($issuedAt)
    {
        if (empty($issuedAt)) {
            $this->issuedAt = null;
        }
    }
}

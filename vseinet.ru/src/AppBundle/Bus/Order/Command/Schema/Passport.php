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
        if (!empty($issuedAt) && preg_match('~^[0-3]\d{1}.[0-1]\d{1}.\d{4}$~isu', $issuedAt)) {
            $this->issuedAt = new \Datetime(date('Y-m-d', strtotime($issuedAt)));
        } elseif (empty($issuedAt)) {
            $this->issuedAt = null;
        } else {
            $this->issuedAt = $issuedAt;
        }
    }
}

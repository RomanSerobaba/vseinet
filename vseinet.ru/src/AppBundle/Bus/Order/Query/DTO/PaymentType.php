<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class PaymentType
{
    /**
     * @Enum("AppBundle\Enum\PaymentTypeCode")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isInternal;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isRemote;

    /**
     * @Assert\Type(type="string")
     */
    public $description;

    public function __construct($code, $name, $isInternal, $isRemote, $description)
    {
        $this->code = $code;
        $this->name = $name;
        $this->isInternal = $isInternal;
        $this->isRemote = $isRemote;
        $this->description = $description;
    }
}

<?php

namespace AdminBundle\Bus\Wholesaler\Query\DTO;

use AppBundle\Validator\Constraints\Enum;
use Symfony\Component\Validator\Constraints as Assert;

class Price
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;
}

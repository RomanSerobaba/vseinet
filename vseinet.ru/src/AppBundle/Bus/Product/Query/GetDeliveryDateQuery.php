<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetDeliveryDateQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
}

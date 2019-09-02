<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message
{
    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;
}

<?php

namespace AppBundle\Bus\Pricetags\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message
{
    /**
     * @Assert\Type("integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type("integer")
     */
    public $geoPointId;
}

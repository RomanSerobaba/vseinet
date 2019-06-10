<?php

namespace AppBundle\Bus\Pricetags\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message
{
    /**
     * @Assert\All(@Assert\Type("integer"))
     */
    public $baseProductIds;

    /**
     * @Assert\Type("integer")
     */
    public $geoPointId;
}

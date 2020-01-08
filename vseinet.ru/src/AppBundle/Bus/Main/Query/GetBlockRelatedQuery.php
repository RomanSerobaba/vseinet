<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetBlockRelatedQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $count = 6;
}

<?php

namespace AdminBundle\Bus\Wholesaler\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetPricesQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="numeric")
     */
    public $baseProductId;
}

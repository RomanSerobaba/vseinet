<?php 

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetOrderQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}

<?php 

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetStatusQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Введите номер заказа")
     * @Assert\Type(type="integer")
     */
    public $number;
}

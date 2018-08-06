<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UnlinkRequestCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Массив requestsIds не должен быть пустым")
     * @Assert\All({@Assert\Type(type="integer")})
     */
    public $requestsIds;
}
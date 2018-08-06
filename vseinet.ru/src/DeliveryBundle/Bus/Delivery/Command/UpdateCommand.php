<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение statusCode не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $statusCode;

    /**
     * @Assert\NotBlank(message="Значение date не должно быть пустым")
     * @Assert\Date()
     */
    public $date;

    /**
     * @Assert\Type(type="integer")
     */
    public $courierId;
}
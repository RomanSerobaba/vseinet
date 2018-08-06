<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение type не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\NotBlank(message="Значение date не должно быть пустым")
     * @Assert\Date()
     */
    public $date;

    /**
     * @Assert\Type(type="integer")
     */
    public $pointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $transportCompanyId;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
}
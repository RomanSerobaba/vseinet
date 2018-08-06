<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SetDetailValueCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение detailId не должно быть пустым")
     * @Assert\Type(type="integer") 
     */
    public $detailId;

    /**
     * @Assert\Type(type="string") 
     */
    public $value;
}
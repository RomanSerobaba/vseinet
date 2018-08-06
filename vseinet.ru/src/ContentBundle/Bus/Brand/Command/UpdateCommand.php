<?php 

namespace ContentBundle\Bus\Brand\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert; 

class UpdateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Url(message="Не правильный URL")
     * @Assert\Type(type="string")
     */
    public $url;
}
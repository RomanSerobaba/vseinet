<?php 

namespace AppBundle\Bus\Geo\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetCityCurrentCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите город")
     * @Assert\Type(type="integer")
     */
    public $id;
}
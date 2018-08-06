<?php 

namespace MatrixBundle\Bus\Template\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="datetime", message="Значение activeFrom должно быть датой")
     */
    public $activeFrom;

    /**
     * @Assert\Type(type="datetime", message="Значение activeTill должно быть датой")
     */
    public $activeTill;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isSeasonal;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
}
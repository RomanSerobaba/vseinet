<?php 

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $delimiterBefore;

    /**
     * @Assert\Type(type="string")
     */
    public $delimiterAfter;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isRequired;
}
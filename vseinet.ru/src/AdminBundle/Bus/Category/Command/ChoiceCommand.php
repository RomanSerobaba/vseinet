<?php 

namespace AdminBundle\Bus\Category\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ChoiceCommand extends Message
{
    /**
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @Assert\NotBlank(message="Выберите категорию")
     * @Assert\Type(type="numeric")
     */
    public $id;
}

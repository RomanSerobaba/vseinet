<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не дожно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank(message="Значение name не дожно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
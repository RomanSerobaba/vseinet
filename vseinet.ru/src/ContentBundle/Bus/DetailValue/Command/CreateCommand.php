<?php 

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение detailId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $detailId;

    /**
     * @Assert\NotBlank(message="Значение value не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
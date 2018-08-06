<?php 

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class MergeCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение mergeId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $mergeId;
}
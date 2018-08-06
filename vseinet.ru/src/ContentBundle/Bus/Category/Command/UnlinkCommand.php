<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UnlinkCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @assert\NotBlank(message="Значение linkToId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $linkToId;
}
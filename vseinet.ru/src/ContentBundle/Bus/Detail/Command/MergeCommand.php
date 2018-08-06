<?php 

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class MergeCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение poolIds не может быть пустым")
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $poolIds;
}
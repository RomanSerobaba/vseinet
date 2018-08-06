<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetIsHiddenCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение ids не должно быть пустым")
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $ids;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isHidden;
}
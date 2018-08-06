<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class MoveCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение ids не должно быть пустым")
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $ids;

    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     */
    public $categoryId;
}
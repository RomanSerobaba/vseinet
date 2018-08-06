<?php 

namespace ContentBundle\Bus\Task\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение managerId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
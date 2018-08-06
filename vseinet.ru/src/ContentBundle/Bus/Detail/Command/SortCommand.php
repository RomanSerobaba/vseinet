<?php 

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SortCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение groupId не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $groupId;

    /**
     * @Assert\NotBlank(message="Значение underId не может быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Под какую характеристику переносится, если на самый верх группы, установить в 0")
     */
    public $underId;
}
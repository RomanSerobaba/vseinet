<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SortCommand extends Message
{   
    /**
     * @Assert\NotBlank(message="Значение id не дожно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение underId не дожно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Под какую группу переносится, если на самый верх установить в 0")
     */
    public $underId;
}
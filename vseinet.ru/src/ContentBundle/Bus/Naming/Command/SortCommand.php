<?php 

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SortCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение underId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Под какой эелемент переносится, если на самый верх, установить в 0")
     */
    public $underId;
}
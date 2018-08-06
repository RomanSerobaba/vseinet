<?php 

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SortCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */    
    public $id;

    /**
     * @Assert\NotBlank(message="Значение paletteId не должно быть пустым")
     * @Assert\Type(type="integer")
     */    
    // public $paletteId;

    /**
     * @Assert\NotBlank(message="Значение underId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Под какой цвет переносится, если на самый верх, установить в 0")
     */    
    public $targetId;
}
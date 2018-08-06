<?php 

namespace AppBundle\Bus\ResourceGroup\Command;

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
     * @Assert\NotBlank(message="Значение underId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Под какую группу ресурсов переносится, если на самый верх, установить в 0")
     */    
    public $underId;  
}
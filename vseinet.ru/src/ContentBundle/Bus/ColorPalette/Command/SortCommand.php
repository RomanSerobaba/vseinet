<?php 

namespace ContentBundle\Bus\ColorPalette\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SortCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Value of 'underId' should not be blank")
     * @Assert\Type(type="integer")
     * @VIA\Description("Под какую палитру переносится, если на самый верх, установить в 0")
     */
    public $underId;
}
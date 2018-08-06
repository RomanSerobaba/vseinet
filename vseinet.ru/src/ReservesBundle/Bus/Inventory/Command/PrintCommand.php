<?php 

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class PrintCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @VIA\Description("Документ инвентаризации")
     */
    public $id;

    /**
     * @VIA\Description("Название формы")
     * @Assert\Type(type="string")
     */
    public $formName;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Название файла (не передавать)")
     */
    public $fileName;
}
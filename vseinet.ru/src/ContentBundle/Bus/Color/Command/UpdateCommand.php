<?php 

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Шестнадцатиричный код")
     * @Assert\NotBlank(message="Value of 'valueHex' should not be blank")
     * @Assert\Type(type="string")
     */
    public $valueHex;

    /**
     * @Assert\NotBlank(message="Value of 'paletteId' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $paletteId;

    /**
     * @VIA\Description("Наименование (м.р.)")
     * @Assert\NotBlank(message="Value of 'nameMale' should not be blank")
     */
    public $nameMale;

    /**
     * @VIA\Description("Наименование (ж.р.)")
     */
    public $nameFemale;

    /**
     * @VIA\Description("Наименование (с.р.)")
     */
    public $nameNeuter;

    /**
     * @VIA\Description("Наименование (дательный падеж)")
     */
    public $nameAblative;

    /**
     * @VIA\Description("Наименование (м.ч.)")
     */
    public $namePlural;

    /**
     * @VIA\Description("Является базовым цветом в палитре")
     * @Assert\Type(type="boolean")
     */
    public $isBase;
}
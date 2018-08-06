<?php 

namespace ContentBundle\Bus\MeasureUnit\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\NotBlank(message="Значение k не должно быть пустым")
     * @Assert\Type(type="float")
     * @VIA\Description("Коэффициент относительно основной единицы измерения (для основной 1)")
     * @VIA\DefaultValue(1)
     */
    public $k;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Ставить пробел между значением и единицей измерения")
     */
    public $useSpace;

    /**
     * @Assert\Type(type="array")
     * @VIA\Description("Псевдонимы")
     */
    public $aliases;
}
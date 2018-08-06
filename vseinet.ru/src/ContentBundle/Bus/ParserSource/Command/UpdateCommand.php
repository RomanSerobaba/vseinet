<?php 

namespace ContentBundle\Bus\ParserSource\Command;

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
     * @Assert\NotBlank(message="Значение code не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\NotBlank(message="Значение alias не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $alias;

    /**
     * @Assert\NotBlank(message="Значение url не должно быть пустым")
     * @Assert\Url
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Включить антизащиту")
     * @VIA\DefaultValue(false)
     */
    public $useAntiGuard;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Парсить изображения")
     * @VIA\DefaultValue(true)
     */
    public $isParseImages;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(true)
     */
    public $isActive;
}
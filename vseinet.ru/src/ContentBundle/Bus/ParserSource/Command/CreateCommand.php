<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{
    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $alias;

     /**
     * @Assert\Type(type="integer")
     */
    public $supplierId;

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

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
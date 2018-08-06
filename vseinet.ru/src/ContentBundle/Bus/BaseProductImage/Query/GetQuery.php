<?php 

namespace ContentBundle\Bus\BaseProductImage\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation AS VIA;

class GetQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Размер изображения")
     * @Assert\Choice({"xs", "sm", "md", "lg", "xl"}, strict=true)
     * @VIA\DefaultValue("md")
     */
    public $size;
}
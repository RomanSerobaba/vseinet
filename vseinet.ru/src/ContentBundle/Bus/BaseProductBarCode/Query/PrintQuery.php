<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class PrintQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор штрихкода")
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @VIA\Description("Формат файла картинки")
     * @Assert\Choice({"png", "svg", "jpg"}, strict=true)
     * @VIA\DefaultValue("png")
     */
    public $formatImage;

}
<?php 

namespace MatrixBundle\Bus\Template\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetQuery extends Message
{    
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор шаблона")
     */
    public $id;    
}
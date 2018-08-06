<?php 

namespace MatrixBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetProductsForOrderQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор точки")
     */
    public $id;
}
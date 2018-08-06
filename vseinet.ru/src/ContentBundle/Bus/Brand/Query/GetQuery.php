<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;
}
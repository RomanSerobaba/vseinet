<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class TreeQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение глубины должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $deep;
}
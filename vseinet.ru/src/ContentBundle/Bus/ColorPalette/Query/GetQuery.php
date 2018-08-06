<?php 

namespace ContentBundle\Bus\ColorPalette\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Вернуть вместе с цветами")
     * @Assert\Type(type="boolean")
     */
    public $colors;
}
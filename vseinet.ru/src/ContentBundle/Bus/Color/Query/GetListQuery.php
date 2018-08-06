<?php 

namespace ContentBundle\Bus\Color\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Value of 'paletteId' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $paletteId;
}
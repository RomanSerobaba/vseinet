<?php 

namespace ContentBundle\Bus\DetailValueAlias\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение valueId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $valueId;
}
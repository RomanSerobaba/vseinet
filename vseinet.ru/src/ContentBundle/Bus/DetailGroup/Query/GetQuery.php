<?php 

namespace ContentBundle\Bus\DetailGroup\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не дожно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;
}
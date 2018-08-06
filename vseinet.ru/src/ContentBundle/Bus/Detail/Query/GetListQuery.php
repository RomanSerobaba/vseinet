<?php 

namespace ContentBundle\Bus\Detail\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение groupId не дожно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $groupId;
}
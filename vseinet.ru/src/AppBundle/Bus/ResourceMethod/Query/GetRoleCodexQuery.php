<?php 

namespace AppBundle\Bus\ResourceMethod\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetRoleCodexQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение resourceId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $resourceId;
}
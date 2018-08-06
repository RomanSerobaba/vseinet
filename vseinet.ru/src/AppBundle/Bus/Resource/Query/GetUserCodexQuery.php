<?php 

namespace AppBundle\Bus\Resource\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetUserCodexQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение userId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $userId;
}
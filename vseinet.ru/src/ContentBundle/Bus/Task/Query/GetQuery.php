<?php 

namespace ContentBundle\Bus\Task\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated
 */
class GetQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не долно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;
}
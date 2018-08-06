<?php 

namespace ContentBundle\Bus\Task\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetListQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение managerId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $managerId;
}
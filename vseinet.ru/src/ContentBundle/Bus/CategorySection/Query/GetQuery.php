<?php 

namespace ContentBundle\Bus\CategorySection\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Не указан код раздела категории")
     * @Assert\Type(type="integer")
     */
    public $id;
}
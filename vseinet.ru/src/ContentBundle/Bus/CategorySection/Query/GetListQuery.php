<?php 

namespace ContentBundle\Bus\CategorySection\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Не указан код категории")
     * @Assert\Type(type="integer")
     */
    public $categoryId;
}
<?php 

namespace ContentBundle\Bus\DetailGroup\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не дожно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;
}
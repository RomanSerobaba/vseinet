<?php 

namespace AppBundle\Bus\ApiMethod\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение sectionId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $sectionId;
}
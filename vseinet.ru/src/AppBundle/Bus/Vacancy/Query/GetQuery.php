<?php 

namespace AppBundle\Bus\Vacancy\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    public $id;
}
<?php 

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetDetailQuery extends Message 
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    public $id;
}

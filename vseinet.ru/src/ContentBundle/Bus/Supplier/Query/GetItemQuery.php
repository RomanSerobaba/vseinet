<?php 

namespace ContentBundle\Bus\Supplier\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetItemQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $id;
}
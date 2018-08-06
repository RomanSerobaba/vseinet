<?php 

namespace ContentBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $id;
}
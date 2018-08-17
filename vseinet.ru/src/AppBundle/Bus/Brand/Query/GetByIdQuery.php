<?php

namespace AppBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetByIdQuery extends Message 
{
    /**
     * @Assert\NotBlank
     * @Assert\type(type="integer")
     */
    public $id;
}

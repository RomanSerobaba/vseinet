<?php 

namespace SiteBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetByNameQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $name;
}

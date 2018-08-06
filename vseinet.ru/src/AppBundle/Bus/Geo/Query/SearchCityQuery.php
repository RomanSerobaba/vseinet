<?php 

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SearchCityQuery extends Message 
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @Assert\Type(type="integer")
     */
    public $limit = 10;
}

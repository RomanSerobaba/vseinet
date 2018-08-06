<?php 

namespace AppBundle\Bus\ResourceMethod\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserCodexItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $apiMethodId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAllowed;


    public function __construct($apiMethodId, $isAllowed)
    {
        $this->apiMethodId = $apiMethodId;
        $this->isAllowed = $isAllowed;
    }
}
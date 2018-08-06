<?php 

namespace AppBundle\Bus\Resource\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserCodexItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $resourceId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAllowed;


    public function __construct($resourceId, $isAllowed)
    {
        $this->resourceId = $resourceId;
        $this->isAllowed = $isAllowed;
    }
}
<?php 

namespace AppBundle\Bus\ResourceMethod\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RoleCodexItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $apiMethodId;

    /**
     * @Assert\Type(type="integer")
     */
    public $subroleId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAllowed;


    public function __construct($apiMethodId, $subroleId, $isAllowed)
    {
        $this->apiMethodId = $apiMethodId;
        $this->subroleId = $subroleId;
        $this->isAllowed = $isAllowed;
    }
}
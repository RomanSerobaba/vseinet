<?php 

namespace AppBundle\Bus\Resource\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RoleCodexItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $resourceId;

    /**
     * @Asset\Type(type="integer")
     */
    public $subroleId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAllowed;


    public function __construct($resourceId, $subroleId, $isAllowed)
    {
        $this->resourceId = $resourceId;
        $this->subroleId = $subroleId;
        $this->isAllowed = $isAllowed;
    }
}
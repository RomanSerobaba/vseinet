<?php 

namespace ContentBundle\Bus\Statistics\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ManagerGroup
{    
    /**
     * @Assert\Type(type="string")
     */
    public $group;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Statistics\Query\DTO\Manager>")
     */
    public $managers;


    public function __construct($group, $managers)
    {
        $this->group = $group;
        $this->managers = $managers;
    }
}
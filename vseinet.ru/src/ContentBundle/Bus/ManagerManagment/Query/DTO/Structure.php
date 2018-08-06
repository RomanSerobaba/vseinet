<?php 

namespace ContentBundle\Bus\ManagerManagment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Structure
{
    /**
     * @Assert\Type(type="array<integer>")
     */
    public $rootIds;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\ManagerManagment\DTO\Department>")
     */
    public $departments;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\ManagerManagment\DTO\ManagerGroup>")
     */
    public $groups = [];

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\ManagerManagment\DTO\Manager>")
     */
    public $managers = [];


    public function __construct($roots, $departments, $groups, $managers) 
    {
        $this->rootIds = array_keys($roots);
        $this->departments = array_values($roots + $departments);
        $this->groups = array_values($groups);
        $this->managers = array_values($managers);
    }
}

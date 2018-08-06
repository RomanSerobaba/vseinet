<?php 

namespace ContentBundle\Bus\ManagerManagment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Department
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $departmentIds = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $groupIds = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $managerIds = [];


    public function __construct($id, $name) 
    {
        $this->id = $id;
        $this->name = $name;
    }
}

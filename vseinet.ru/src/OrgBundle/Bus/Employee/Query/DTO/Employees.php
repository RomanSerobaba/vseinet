<?php 

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Employees

{    
    /**
     * @Assert\Type(type="array<OrgBundle\Bus\Employee\Query\DTO\Employee>")
     */
    public $employees;

    /**
     * @Assert\Type(type="array<OrgBundle\Bus\Employee\Query\DTO\Department>")
     */
    public $departments;

    public function __construct($employees, $departments)
    {
        $this->employees = $employees;
        $this->departments = $departments;
    }

}
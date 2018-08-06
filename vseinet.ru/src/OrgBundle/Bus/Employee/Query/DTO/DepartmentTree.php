<?php 

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DepartmentTree

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
     * @Assert\Type(type="array")
     */
    public $departments;
//     * @Assert\Type(type="array<OrgBundle\Bus\Employee\Query\DTO\DepartmentTree>")

    /**
     * @Assert\Type(type="array<OrgBundle\Bus\Employee\Query\DTO\EmployeeTree>")
     */
    public $employees;

}
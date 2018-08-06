<?php 

namespace OrgBundle\Bus\Employee\Query\DTO;

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
    public $departmentsIds;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $employeesIds;

    public function __construct(int $id, string $name, $departmentsIds, $employeesIds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->departmentsIds = $departmentsIds;
        $this->employeesIds = $employeesIds;
    }

}
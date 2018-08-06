<?php 

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Employee
{    
    
    /**
     * Идентификатор сотрудника
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * Признак увольнения
     * @Assert\Type(type="boolean")
     */
    public $isFired;

    /**
     * Ф.И.О. сотрудника
     * @Assert\Type(type="string")
     */
    public $name;

    public function __construct(int $id, $isFired = false, string $name)
    {
        $this->id = $id;
        $this->isFired = $isFired;
        $this->name = $name;
    }

}
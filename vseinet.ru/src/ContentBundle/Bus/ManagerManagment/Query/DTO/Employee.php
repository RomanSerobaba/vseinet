<?php 

namespace ContentBundle\Bus\ManagerManagment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Employee
{
    /**
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;


    public function __construct($userId, $name) 
    {
        $this->userId = $userId;
        $this->name = $name;
    }
}

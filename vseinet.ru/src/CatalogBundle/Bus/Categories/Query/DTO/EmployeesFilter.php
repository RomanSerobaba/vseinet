<?php 

namespace CatalogBundle\Bus\Categories\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeesFilter
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $fullname;

    /**
     * EmployeesFilter constructor.
     * @param $id
     * @param $fullname
     */
    public function __construct($id, $fullname)
    {
        $this->id = $id;
        $this->fullname = $fullname;
    }
}
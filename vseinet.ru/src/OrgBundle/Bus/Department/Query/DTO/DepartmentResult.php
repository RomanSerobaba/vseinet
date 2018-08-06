<?php

namespace OrgBundle\Bus\Department\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DepartmentResult
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
     * @Assert\Type(type="string")
     */
    public $number;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $path;

    /**
     * DepartmentResult constructor.
     * @param $id
     * @param $name
     * @param $number
     * @param $typeCode
     * @param $path
     */
    public function __construct(
        $id=null,
        $name=null,
        $number=null,
        $typeCode=null,
        $path=null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->number = $number;
        $this->typeCode = $typeCode;
        $this->path = $path;
    }
}
<?php

namespace OrgBundle\Bus\Work\Command\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeePayment
{
    /**
     * @var int
     *
     * @Assert\Type(type="numeric")
     */
    public $employeeUserId;

    /**
     * @var string
     *
     * @Assert\Type(type="DateTime")
     */
    public $date;
}
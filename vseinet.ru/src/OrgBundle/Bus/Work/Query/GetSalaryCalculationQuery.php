<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetSalaryCalculationQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение employee id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $date;
}
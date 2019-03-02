<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SettlementAccount extends Constraint
{
    public $message = 'Номер расчетного счёта должен состоять из 20 цифр';
}

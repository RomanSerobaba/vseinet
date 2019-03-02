<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class KPP extends Constraint
{
    public $message = 'КПП должен состоять из 9 цифр';
}

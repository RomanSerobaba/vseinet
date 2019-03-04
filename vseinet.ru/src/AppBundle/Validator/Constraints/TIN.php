<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TIN extends Constraint
{
    public $message = 'Неверный ИНН.';
}

<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BIC extends Constraint
{
    $message = 'Неверный БИК.';
}

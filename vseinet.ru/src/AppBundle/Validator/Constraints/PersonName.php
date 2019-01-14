<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PersonName extends Constraint
{
    public $message = 'Имя может содержать только буквы, пробелы или дефисы.';
}

<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MobilePhone extends Constraint
{
    public $message = 'Неверный формат номера мобильного телефона.';
}
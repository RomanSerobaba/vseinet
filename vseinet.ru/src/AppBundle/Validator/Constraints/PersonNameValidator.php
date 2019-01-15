<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null !== $value) {
            if (preg_match('~[^а-яА-Яa-zA-Z-\s]~isu', $value, $matches)) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}

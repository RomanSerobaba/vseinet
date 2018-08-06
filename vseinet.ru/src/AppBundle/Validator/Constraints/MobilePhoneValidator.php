<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MobilePhoneValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null !== $value) {
            $phone = preg_replace(['~^(\+7|8)~isu', '~[\s\(\)\-,\+]~isu'], '', $value);
            if (!preg_match('~^\d{10}$~isu', $phone, $matches)) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BICValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BIC) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\BIC');
        }

        if (empty($value)) {
            return;
        }

        $isValid = preg_match('/^(\d{2})(\d{4})(\d{3})$/', str_replace(' ', '', $value), $m);

        if ($isValid) {
            $isValid = 4 == $m[1] && ((0 <= $m[3] && $m[3] <= 2) || 50 <= $m[3]);
        }

        if (!$isValid) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

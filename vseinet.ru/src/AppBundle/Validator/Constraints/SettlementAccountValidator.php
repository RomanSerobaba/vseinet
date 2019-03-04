<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SettlementAccountValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SettlementAccount) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\SettlementAccount');
        }

        if (empty($value)) {
            return;
        }

        if (!preg_filter('/^(\d{20})$/ui', '$1', str_replace(' ', '', $value))) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

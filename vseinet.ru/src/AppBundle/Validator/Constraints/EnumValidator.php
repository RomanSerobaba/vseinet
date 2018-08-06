<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EnumValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Enum) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Enum');
        }

        if (!class_exists($constraint->ref)) {
            throw new ConstraintDefinitionException('Class "ref" must be specified on constraint Enum');
        }

        if (null === $value) {
            return;
        }

        $reflector = new \ReflectionClass($constraint->ref);
        $constants = $reflector->getConstants();
        if (!in_array($value, $constants, $constraint->strict)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            
        }
    }
}

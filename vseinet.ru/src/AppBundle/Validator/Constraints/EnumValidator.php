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

        if ($constraint->choices) {
            if (is_string($constraint->choices)) {
                $getter = 'get'.str_replace('_', '', ucwords($constraint->choices, '_'));
                $choices = call_user_func([$constraint->ref, $getter]);
                if (!is_array($choices)) {
                    throw new ConstraintDefinitionException(
                        sprintf('Method "%s::%s" should be returns an array', $constraint->ref, $getter)
                    );
                }
            } elseif (is_array($constraint->choices)) {
                $choices = $constraint->choices;
            } else {
                throw new ConstraintDefinitionException('Property "choices" must be an array or function on constraint Enum');
            }
            
            $invalid = array_diff($choices, $constants);
            if ($invalid) {
                throw new ConstraintDefinitionException(
                    sprintf('Constants [%s] not found in class "%s"', implode(', ', $invalid), $constraint->ref)
                );
            }

            $constants = array_intersect($constants, $choices);
        }

        if (!in_array($value, $constants, $constraint->strict)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            
        }
    }
}

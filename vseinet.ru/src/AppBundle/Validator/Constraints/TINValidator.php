<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Алгоритм проверки ИНН 10 знаков:
 * 1. Вычисляется контрольная сумма со следующими весовыми коэффициентами: (2,4,10,3,5,9,4,6,8,0)
 * 2. Вычисляется контрольное число как остаток от деления контрольной суммы на 11
 * 3. Если контрольное число больше 9, то контрольное число вычисляется как остаток от деления контрольного числа на 10
 * 4. Контрольное число проверяется с десятым знаком ИНН. В случае их равенства ИНН считается правильным.
 *
 * Алгоритм проверки ИНН 12 знаков:
 * 1. Вычисляется контрольная сумма по 11-ти знакам со следующими весовыми коэффициентами: (7,2,4,10,3,5,9,4,6,8,0)
 * 2. Вычисляется контрольное число(1) как остаток от деления контрольной суммы на 11
 * 3. Если контрольное число(1) больше 9, то контрольное число(1) вычисляется как остаток от деления контрольного числа(1) на 10
 * 4. Вычисляется контрольная сумма по 12-ти знакам со следующими весовыми коэффициентами: (3,7,2,4,10,3,5,9,4,6,8,0).
 * 5. Вычисляется контрольное число(2) как остаток от деления контрольной суммы на 11
 * 6. Если контрольное число(2) больше 9, то контрольное число(2) вычисляется как остаток от деления контрольного числа(2) на 10
 * 7. Контрольное число(1) проверяется с одиннадцатым знаком ИНН и контрольное число(2) проверяется с двенадцатым знаком ИНН.
 * В случае их равенства ИНН считается правильным.
 */
class TINValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TIN) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\TIN');
        }

        if (empty($value)) {
            return;
        }

        $value = preg_filter('/^(\d{10}(\d{2})?)$/ui', '$1', str_replace(' ', '', $value));
        if (!$value) {
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        $k = [2, 4, 10, 3, 5, 9, 4, 6, 8];
        $n = str_split($value);
        $index = 9;
        $length = count($n);
        if (12 == $length) {
            $index = 10;
            array_unshift($k, 7);
        }

        $s = array_sum(array_map(function ($n, $k) {
            return $n * $k;
        }, $n, $k));

        if ($s % 11 % 10 !== intval($n[$index])) {
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        if (12 == $length) {
            array_unshift($k, 3);
            $s = array_sum(array_map(function ($n, $k) {
                return $n * $k;
            }, $n, $k));

            if ($s % 11 % 10 !== intval($n[11])) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}

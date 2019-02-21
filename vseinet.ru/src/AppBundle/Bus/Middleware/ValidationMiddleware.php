<?php

namespace AppBundle\Bus\Middleware;

use League\Tactician\Middleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationMiddleware implements Middleware
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     *
     * @throws ValidationException
     */
    public function execute($command, callable $next)
    {
        $violations = $this->validator->validate($command);

        if (0 != count($violations)) {
            $exception = null;
            foreach ($violations as $violation) {
                $exception = new ValidationException($violation->getPropertyPath(), $violation->getMessage(), $exception);
            }

            throw new BadRequestHttpException('Ошибки валидации входящих параметров', $exception);
        }

        return $next($command);
    }
}

<?php 

namespace AppBundle\Bus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Bus\Exception\ValidationException;

class CommandValidationMiddleware implements MessageBusMiddleware
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Dependency Injection constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $violations = $this->validator->validate($message);

        if (count($violations) != 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new ValidationException($errors);
        }

        $next($message);
    }
} 
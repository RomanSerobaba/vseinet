<?php 

namespace AppBundle\Bus\Middleware;

use Ajgl\SimpleBus\Message\Bus\Middleware\CatchReturnMessageBusMiddleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Bus\Exception\ValidationException;

class QueryValidationMiddleware implements CatchReturnMessageBusMiddleware
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
    public function handle($message, callable $next, &$return = null)
    {
        $violations = $this->validator->validate($message);

        if (0 != count($violations)) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new ValidationException($errors);
        }

        $return = $next($message);
    }
} 
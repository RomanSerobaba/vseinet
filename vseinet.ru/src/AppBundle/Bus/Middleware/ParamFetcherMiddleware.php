<?php

namespace AppBundle\Bus\Middleware;

use League\Tactician\Middleware;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Annotations\Reader;
use AppBundle\Bus\Message\Message;
use AppBundle\Validator\Constraints\Enum;

class ParamFetcherMiddleware implements Middleware
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        return $next($this->fetch($command));
    }

    /**
     * @param object $command
     *
     * @return object
     */
    protected function fetch($command)
    {
        $reflection = new \ReflectionClass($command);
        foreach ($reflection->getProperties() as $property) {
            $value = $property->getValue($command);
            if (null !== $value) {
                $value = $this->fetchProperty($property, $value);
            }
            $propertyName = $property->getName();
            $propertySetter = 'set'.ucfirst($propertyName);
            if (method_exists($command, $propertySetter)) {
                $command->$propertySetter($value);
            } else {
                $command->$propertyName = $value;
            }
        }

        return $command;
    }

    /**
     * Get value with type cast.
     *
     * @param \ReflectionProperty $property
     * @param mixin               $value
     *
     * @return mixin
     */
    protected function fetchProperty(\ReflectionProperty $property, $value)
    {
        $annotations = $this->reader->getPropertyAnnotations($property);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Enum) {
                return $this->fetchValue($value, 'enum');
            }
            if ($annotation instanceof Assert\Type) {
                return $this->fetchValue($value, $annotation->type);
            }
            if ($annotation instanceof Assert\Date) {
                return $this->fetchValue($value, 'date');
            }
            if ($annotation instanceof Assert\DateTime) {
                return $this->fetchValue($value, 'datetime');
            }
            if ($annotation instanceof Assert\All) {
                if (empty($value)) {
                    return null;
                }
                foreach ($annotation->constraints as $constraint) {
                    if ($constraint instanceof Enum) {
                        foreach ($value as $index => $v) {
                            $value[$index] = $this->fetchValue($v, 'enum');
                        }

                        return $value;
                    }
                    if ($constraint instanceof Assert\Type) {
                        foreach ($value as $index => $v) {
                            $value[$index] = $this->fetchValue($v, $constraint->type);
                        }

                        return $value;
                    }
                    if ($annotation instanceof Assert\Date) {
                        foreach ($value as $index => $v) {
                            $value[$index] = $this->fetchValue($v, 'date');
                        }

                        return $value;
                    }
                    if ($annotation instanceof Assert\DateTime) {
                        foreach ($value as $index => $v) {
                            $value[$index] = $this->fetchValue($v, 'datetime');
                        }

                        return $value;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * Get value with type cast.
     *
     * @param mixin  $value
     * @param string $type
     *
     * @return mixin
     */
    protected function fetchValue($value, $type)
    {
        if ('string' === $type) {
            return (string) $value;
        }

        if (is_string($value) && 0 === strlen($value)) {
            return null;
        }

        $filters = [
            'boolean' => FILTER_VALIDATE_BOOLEAN,
            'integer' => FILTER_VALIDATE_INT,
            'float' => FILTER_VALIDATE_FLOAT,
        ];
        if (!empty($filters[$type])) {
            return filter_var($value, $filters[$type], FILTER_NULL_ON_FAILURE) ?? $value;
        }

        if ('date' === $type || 'datetime' === $type) {
            try {
                if (!$value instanceof \DateTimeInterface) {
                    $value = new \DateTime($value);
                }
                if ('date' === $type) {
                    $value->setTime(0, 0);
                }
            } catch (\Exception $e) {
            }

            return $value;
        }

        if (class_exists($type)) {
            $message = new $type($value);
            if (!$message instanceof Message) {
                throw new \InvalidArgumentException(
                    sprintf('Property "%s" must be inherits from "AppBundle\Bus\Message\Message".', $type)
                );
            }

            return $this->fetch($message);
        }

        return $value;
    }
}

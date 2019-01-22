<?php 

namespace AppBundle\Bus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\Constraints as Assert;

class CommandBuilderMiddleware implements MessageBusMiddleware
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Dependency Injection constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $this->build($message);
        $next($message);
    }

    protected function build($object)
    {
        $reflector = new \ReflectionClass($object);
        foreach ($reflector->getProperties() as $property) {
            $value = $property->getValue($object);
            if (is_array($value)) {
                $annotations = $this->reader->getPropertyAnnotations($property);    
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Assert\Type) {
                        $nestedClass = $annotation->type;
                        if (class_exists($nestedClass)) {
                            $nestedObject = new $nestedClass($value);
                            $this->build($nestedObject);
                            $property->setValue($object, $nestedObject);
                        }
                        continue;
                    }
                    if ($annotation instanceof Assert\All) {
                        foreach ($annotation->constraints as $constraint) {
                            if ($constraint instanceof Assert\Type) {
                                $nestedClass = $constraint->type;
                                if (class_exists($nestedClass)) {
                                    $arrayOfNestedObjects = [];
                                    foreach ($value as $v) {
                                        $nestedObject = new $nestedClass($v);
                                        $this->build($nestedObject);
                                        $arrayOfNestedObjects[] = $nestedObject;
                                    }
                                    $property->setValue($object, $arrayOfNestedObjects);
                                }
                            }      
                        }
                        continue;
                    }
                }
            }
        }
    }
}

<?php 

namespace AppBundle\EventListener;

use AppBundle\ApiDoc\Generator;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ParamFetcherListener
{
    /**
     * @var Generator
     */
    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        list($controller, $action) = $controller; 
        $parameters = $this->generator->getMethodParameters($controller, $action);
        if (empty($parameters)) {
            return;
        }

        $request = $event->getRequest();

        foreach ($parameters as $name => $attributes) {
            switch ($attributes['in']) {
                case 'query':
                    $value = $request->query->get($name);
                    break;

                case 'body':
                    $value = $request->request->get($name);
                    break;

                default:
                    continue 2;                    
            }
            if (null === $value) {
                if (array_key_exists('default', $attributes)) {
                    $value = $attributes['default'];
                }    
            }

            $value = $this->fetch($value, $attributes['type']);

            switch ($attributes['in']) {
                case 'query':
                    $request->query->set($name, $value);
                    break;

                case 'body':
                    $request->request->set($name, $value);
                    break;

                default:
                    continue 2;
            }
        }
    }

    protected function fetch($value, $type)
    {
        if ('boolean' == $type) {
            return null === $value ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN);    
        }
        if ('float' == $type) {
            $value = filter_var($value, FILTER_VALIDATE_FLOAT);    

            return false === $value ? null : $value;
        }
        if ('integer' == $type) {
            $value = filter_var($value, FILTER_VALIDATE_INT);

            return false === $value ? null : $value;
        }
        if ('datetime' == $type) {
            if (empty($value)) {
                return null;
            }
            if (false !== strpos($value, ' ')) {
                $value = str_replace(' ', '+', $value);
            }
            $value = new \DateTime($value);
        }

        if (false !== strpos($type, 'array of')) {
            if (!is_array($value)) {
                return null;
            }
            $type = substr($type, 9);
            foreach ($value as $index => $v) {
                $value[$index] = $this->fetch($v, $type);
            }

        }

        return $value;
    }
}

<?php 

namespace AppBundle\ApiDoc;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use AppBundle\Annotation AS VIA;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Metadata\MetadataFactoryInterface;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use AppBundle\Validator\Constraints as VIC;


class Generator
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Controller reflectors
     */
    protected $reflectors;

    /**
     * Controller APIs
     */
    protected $docs;

    public function __construct(RouterInterface $router, Reader $reader, MetadataFactoryInterface $factory, SerializedNameAnnotationStrategy $namingStrategy) 
    {
        $this->router = $router;
        $this->reader = $reader;
        $this->factory = $factory;
        $this->namingStrategy = $namingStrategy;
    }

    public function generate()
    {   
        $docs = []; 
        $routes = $this->router->getRouteCollection();
        foreach ($routes as $route) {
            if (false === strpos($route->getDefault('_controller'), '::')) {
                continue;
            }
            list($controller, $action) = explode('::', $route->getDefault('_controller'));

            $reflector = $this->getControllerReflector($controller);
            $controllerDoc = $this->getControllerDoc($reflector);
            $actionDoc = $this->getActionDoc($reflector->getMethod($action));

            $doc = $this->merge($controllerDoc, $actionDoc);
            $doc['route']['path'] = $route->getPath();

            $doc['parameters'] = [];
            $parameters = $this->getMethodParameters($controller, $action);
            foreach ($parameters as $name => $attributes) {
                if ('header' == $attributes['in']) {
                    $doc['parameters'][$name] = $attributes;
                }
            }
            foreach ($parameters as $name => $attributes) {
                if ('path' == $attributes['in']) {
                    $doc['parameters'][$name] = $attributes;
                }
            }
            foreach ($parameters as $name => $attributes) {
                if ('query' == $attributes['in']) {
                    $doc['parameters'][$name] = $attributes;
                }
            }
            foreach ($parameters as $name => $attributes) {
                if ('body' == $attributes['in']) {
                    $doc['parameters'][$name] = $attributes;
                }
            }
            $docs[] = $doc;
        }

        return $docs;
    }

    protected function getControllerReflector($controller)
    {
        if (!isset($this->reflectors[$controller])) {
            $this->reflectors[$controller] = new ReflectionClass($controller);
        }

        return $this->reflectors[$controller];
    }
    
    protected function getControllerDoc(ReflectionClass $reflector) 
    {
        if (!isset($this->docs[$reflector->getName()])) {
            $annotations = $this->reader->getClassAnnotations($reflector);
            $this->docs[$reflector->getName()] = $this->getDoc($annotations, true);
        }

        return $this->docs[$reflector->getName()];
    }

    protected function getActionDoc(ReflectionMethod $method)
    {
        $annotations = $this->reader->getMethodAnnotations($method);

        return $this->getDoc($annotations);
    }

    protected function getDoc($annotations, $isController = false)
    {
        $doc = [];
        foreach ($annotations as $annotation) {
            if ($annotation instanceof VIA\Section) {
                $doc['section'] = $annotation->name;
                continue;
            }
            if ($annotation instanceof VIA\Route) {
                $doc['route'] = [
                    'method' => $annotation->getMethod(),
                    'path' => $annotation->getPath(),
                    'name' => $annotation->getTitle(),
                    'description' => $annotation->getDescription(),
                ];
                // @todo remove it
                if (empty($doc['route']['name'])) {
                    $doc['route']['name'] = $doc['route']['description'];
                }
                elseif (empty($doc['route']['description'] )) {
                    $doc['route']['description'] = $doc['route']['name'];
                }
                continue;
            }
            if ($annotation instanceof VIA\Response) {
                $doc['responses'][$annotation->status]['description'] = $annotation->description;
                if (!empty($annotation->properties)) {
                    $properties = $this->getParameters($annotation->properties);
                    if (!empty($properties)) {
                        $doc['responses'][$annotation->status]['properties'] = $properties;
                    }
                }
            }
        }
        
        return $doc;
    }

    public function getMethodParameters($controller, $action)
    {
        $reflector = new ReflectionMethod($controller, $action);
        $annotations = $this->reader->getMethodAnnotations($reflector);

        $parameters = [];

        foreach ($annotations as $annotation) {
            if ($annotation instanceof VIA\Route) {

                $method = $annotation->getMethod();

                if (preg_match_all('/{([^}]+)}/', $annotation->getPath(), $matches)) {
                    foreach($matches[1] as $name) {
                        $parameters[$name] = [
                            'in' => 'path',
                            'required' => true,
                        ];
                    }
                }

                foreach ($this->getParameters($annotation->getParameters()) as $name => $attributes) {
                    if (isset($parameters[$name])) {
                        foreach ($attributes as $attribute => $value) {
                            $parameters[$name][$attribute] = $value;
                        }
                    }
                    else {
                        $parameters[$name] = $attributes;
                    }

                    if (empty($parameters[$name]['type'])) {
                        $parameters[$name]['type'] = 'string';
                    }
                    if (empty($parameters[$name]['in'])) {
                        $parameters[$name]['in'] = in_array($annotation->getMethod(), ['GET', 'LINK', 'UNLINK']) ? 'query' : 'body';
                    }
                }

                break;
            }    
        }

        return $parameters;
    }

    protected function getParameters($schema)
    {
        $parameters = [];
        foreach ($schema as $parameter) {
            if (null === $parameter->name) {
                $parametersFromSchema = [];
                if ($parameter->model) {
                    $parametersFromSchema = $this->getParametersFromSchema($parameter->model);  
                }
                if ('array' == $parameter->type) {
                    $parametersFromSchema = [
                        'type' => 'array of object',
                        'schema' => $parametersFromSchema,
                    ];
                }
                elseif (false !== strpos($parameter->type, 'array<')) {
                    $type = str_replace(['array<', '>'], '', $parameter->type);
                    $parametersFromSchema = [
                        'type' => 'array of '.$type,
                    ];
                }
                $parameters = array_merge($parameters, $parametersFromSchema);
                continue;
            }
            $parameters[$parameter->name] = [];
            if ($parameter->type) {
                $parameters[$parameter->name]['type'] = $parameter->type;   
            }
            if ($parameter->model) {
                $parameters[$parameter->name]['type'] = 'array' == $parameter->type ? 'array of object' : 'object';
                $parameters[$parameter->name]['schema'] = $this->getParametersFromSchema($parameter->model);
            }
            if (!empty($parameter->in)) {
                $parameters[$parameter->name]['in'] = $parameter->in;     
            }
            if (!empty($parameter->required)) {
                $parameters[$parameter->name]['required'] = $parameter->required;     
            }
            if ($parameter->description) {
                $parameters[$parameter->name]['description'] = $parameter->description;
            }
        }

        return $parameters;
    }

    protected function getParametersFromSchema($schema)
    {
        $reflector = new ReflectionClass($schema);
        $annotations = $this->reader->getClassAnnotations($reflector);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ORM\Entity) {
                $parameters = $this->getParametersFromEntity($schema);
                foreach ($reflector->getProperties() as $property) {
                    $annotations = $this->reader->getPropertyAnnotations($property);
                    foreach ($annotations as $annotation) {
                        if ($annotation instanceof VIA\Description) {
                            $parameters[$property->getName()]['description'] = $annotation->value;
                            continue;
                        }
                        if ($annotation instanceof Assert\Type) {
                            if (false !== strpos($annotation->type, 'array<')) {
                                $type = $this->getPropertyType($annotation->type);
                                if (null === $type) {
                                    $type = 'object';
                                }
                                $parameters[$property->getName()]['type'] = 'array of '.str_replace(['array<', '>'], '', $type);
                            }
                            else {
                                $parameters[$property->getName()]['type'] = $this->getPropertyType($annotation->type);
                            }
                            continue;  
                        }
                    }
                }   

                return $parameters;             
            }
        }
        $parameters = [];
        foreach ($reflector->getProperties() as $property) {
            $annotations = $this->reader->getPropertyAnnotations($property);
            if ($params = $this->getParametersFromAnnotaions($annotations)) {
                $parameters[$property->getName()] = $params;
            }
        }  

        return $parameters;
    }

    protected function getParametersFromAnnotaions($annotations, $excludeModel = null)
    {
        $parameters = [];
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Assert\Uuid) {
                continue;
            }
            if ($annotation instanceof Assert\All) {
                $parameters = array_merge($parameters, $this->getParametersFromAnnotaions($annotation->constraints));
                if (isset($parameters['type'])) {
                    $parameters['type'] = 'array of '.$parameters['type'];
                }
                continue;
            }
            if ($annotation instanceof Assert\Callback) {
                $parameters['type'] = $annotation->callback[0];
                $model = $annotation->callback[0];
                if (class_exists($model)) {
                    $parameters['schema'] = $this->getParametersFromSchema($model);    
                }
                continue;
            }
            if ($annotation instanceof Assert\Type) {
                if ($annotation->type) {
                    if (false !== strpos($annotation->type, 'array<')) {
                        $parameters['type'] = 'array';
                        $model = str_replace(['array<', '>'], '', $annotation->type);
                    }
                    else {
                        $parameters['type'] = $annotation->type;
                        $model = $annotation->type;
                    }
                    if (class_exists($model)) {
                        if ('array' == $parameters['type']) {
                            $parameters['type'] = 'array of object';
                        }
                        $parameters['schema'] = $this->getParametersFromSchema($model);    
                    }
                    elseif ('array' == $parameters['type']) {
                        $parameters['type'] = 'array of '.$model;
                    }
                }
                continue;
            }
            if ($annotation instanceof VIA\Money) {
                $parameters['type'] = 'float';
                $parameters['currency'] = $annotation->getCurrency();
            }
            if ($annotation instanceof Assert\NotBlank) {
                $parameters['required'] = true; 
                continue;   
            }
            if ($annotation instanceof Assert\Choice) {
                $parameters['type'] = 'choice';
                $parameters['choices'] = $annotation->choices;
                $parameters['multiple'] = $annotation->multiple;
            }
            if ($annotation instanceof VIA\DefaultValue) {
                $parameters['default'] = $annotation->value;
            }
            if ($annotation instanceof VIA\Description) {
                $parameters['description'] = $annotation->value;
                continue;
            }
            if ($annotation instanceof ORM\Column) {
                $parameters['type'] = $annotation->type;
                continue;
            }
            if ($annotation instanceof VIC\Enum) {
                $ref = new \ReflectionClass($annotation->ref);
                $parameters['type'] = 'choice';
                $parameters['choices'] = array_values($ref->getConstants());
                $parameters['multiple'] = $annotation->multiple;
                continue;
            }
        }

        return $parameters;
    }

    protected function getParametersFromEntity($class)
    {
        $properties = [];
        $metadata = $this->factory->getMetadataForClass($class); 
        foreach ($metadata->propertyMetadata as $item) {
            if (null === $item->type) {
                continue;
            }

            $name = $this->namingStrategy->translateName($item);
            $property = [];

            if ($type = $this->getNestedTypeInArray($item)) {
                $property['type'] = 'array';
                $nestedType = $this->getPropertyType($type);
                if (null === $nestedType) {
                    if (!class_exists($type)) {
                        continue;
                    }
                    $property['type'] = 'array of object';
                    $property['schema'] = $this->getParametersFromEntity($type);
                }
                else {
                    $property['type'] = 'array of '.$type;
                }
                $properties[$name] = $property;
                continue;
            } 

            $type = $item->type['name'];
            $property['type'] = $this->getPropertyType($type);
            if (null === $property['type']) {
                if (!class_exists($type)) {
                    continue;
                }
                $property['type'] = 'object';
                $property['schema'] = $this->getParametersFromEntity($type);
            }
            $properties[$name] = $property;
        }  

        return $properties;      
    }

    protected function getPropertyType($type)
    { 
        if (in_array($type, array('boolean', 'integer', 'string', ' float', 'array'))) {
            return $type;
        } 
        if ('double' === $type) {
            return 'float';
        } 
        if ('DateTime' === $type || 'DateTimeImmutable' === $type) {
            return 'datetime';
        }

        return null;
    }

    protected function getNestedTypeInArray(PropertyMetadata $item)
    {
        if ('array' !== $item->type['name'] && 'ArrayCollection' !== $item->type['name']) {
            return;
        }

        // array<string, MyNamespaceMyObject>
        if (isset($item->type['params'][1]['name'])) {
            return $item->type['params'][1]['name'];
        }

        // array<MyNamespaceMyObject>
        if (isset($item->type['params'][0]['name'])) {
            return $item->type['params'][0]['name'];
        }
    }

    protected function merge($controllerDoc, $actionDoc)
    {
        $doc = $controllerDoc;
        foreach ($actionDoc as $key => $value) {
            switch ($key) {
                // @todo remove it
                case 'pages':
                    if (isset($doc['pages'])) {
                        $doc['pages'] = array_unique(array_merge($doc['pages'], $value));
                        break;
                    }
                    $doc['pages'] = $value;
                    break;

                default:
                    if (isset($doc[$key])) {
                        $doc[$key] = array_merge($doc[$key], $value);
                        break;
                    }    
                    $doc[$key] = $value;
            }
        }

        return $doc;
    }
}
<?php 
namespace AppBundle\Doctrine\ORM\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Inflector\Inflector;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class DTOHydrator extends AbstractHydrator 
{     
    const AVAILABLE_TYPES = ['string', 'integer', 'float', 'boolean', 'date', 'datetime'];

    /**
     * @var array
     */
    protected $mapKeys;

    /**
     * @var \ReflectionClass
     */
    protected $reflector;

    /**
     * @inheritdoc
     */
    protected function hydrateAllData()     
    {         
        $result = [];   

        while ($row = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($row, $result);
            if ($this->_rsm->getMode() === DTORSM::OBJECT_SINGLE) {
                return $result[0];
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function hydrateRowData(array $row, array &$result)
    {
        if (null === $this->mapKeys) {
            $this->map($row);
        }

        $dto = $this->reflector->newInstanceWithoutConstructor();
        foreach ($this->mapKeys as $key => $map) {
            $setter = 'set'.ucfirst($map['property']);
            if (method_exists($dto, $setter)) {
                $dto->$setter($row[$key]);
                continue;
            }
            $value = $this->fetchValue($row[$key], $map['type'], $map['subtype']);
            if (null === $value) {
                continue;
            }
            $property = $this->reflector->getProperty($map['property']);
            $property->setAccessible(true);
            $property->setValue($dto, $value);
        }

        switch ($this->_rsm->getMode()) {
            case DTORSM::ARRAY_INDEX:
            case DTORSM::OBJECT_SINGLE:
                $result[] = $dto;
                break;

            case DTORSM::ARRAY_ASSOC:
                $property = reset($this->mapKeys)['property'];
                $result[$dto->$property] = $dto;
                break;
        } 
    }

    /**
     * Fetching value.
     * 
     * @param string|mixin $value;
     * @param string $type
     * @param string|null $subtype
     * 
     * @throws \UnexpectedValueException
     * 
     * @return mixin
     */
    protected function fetchValue($value, $type, $subtype)
    {
        if ('string' === $type) {
            return $value;
        }

        if ('integer' === $type) {
            $value = filter_var($value, FILTER_VALIDATE_INT);

            return false === $value ? null : $value;
        }

        if ('float' === $type) {
            $value = filter_var($value, FILTER_VALIDATE_FLOAT);
            
            return false === $value ? null : $value;    
        }

        if ('boolean' === $type) {
            if (null === $value) {
                return null;
            }
            // from psql
            if ('t' === $value) {
                return true; 
            }
            // from psql
            if ('f' === $value) {
                return false; 
            }

            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ('date' === $type || 'datetime' === $type) {
            $value = strtotime($value);
            if (false === $value) {
                return null;
            }

            $format = 'date' === $type ? 'Y-m-d' : 'Y-m-d H:i:s';

            return new \DateTime(date($format, $value));
        }

        if ('array' === $type) {
            $array = json_decode($value, true);
            if (null !== $subtype) {
                foreach ($array as $index => $value) {
                    $array[$index] = $this->fetchValue($value, $subtype, null);
                }
            }

            return $array;
        }
        
        throw new \UnexpectedValueException(sprintf('Can not hydrate value "%s".', $value));
    }

    /**
     * Mapping keys row to DTO properties
     * 
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function map($row)
    {
        if (!$this->_rsm instanceof DTORSM) {
            throw new \InvalidArgumentException('ResultSetMapping must instance of DTORSM');
        }

        $snakeKeys = array_keys($row);
        $camelKeys = array_map(function($key) { return Inflector::camelize($key); }, $snakeKeys);

        $reader = new AnnotationReader();
        $this->reflector = new \ReflectionClass($this->_rsm->getDTO());
        foreach ($this->reflector->getProperties() as $property) {
            $indexKey = array_search($property->getName(), $camelKeys);
            if (false === $indexKey) {
                continue;
            }
            $this->mapKeys[$snakeKeys[$indexKey]]['property'] = $property->getName();
            $annotations = $reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Assert\Type) {
                    if (in_array($annotation->type, self::AVAILABLE_TYPES)) { 
                        $this->mapKeys[$snakeKeys[$indexKey]]['type'] = $annotation->type;
                        $this->mapKeys[$snakeKeys[$indexKey]]['subtype'] = null;
                        continue;
                    }
                    if (0 === strpos($annotation->type, 'array')) {
                        $subtype = str_replace(['array', '<', '>'], '', $annotation->type);
                        if (in_array($subtype, self::AVAILABLE_TYPES)) {
                            $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'array';
                            $this->mapKeys[$snakeKeys[$indexKey]]['subtype'] = $subtype;
                        }
                    }
                    continue;
                }
                if ($annotation instanceof Enum) {
                    $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'string';
                    $this->mapKeys[$snakeKeys[$indexKey]]['subtype'] = null;
                }
            }
        }
        if (count($snakeKeys) !== count($this->mapKeys)) {
            $lostKeys = array_diff($snakeKeys, array_keys($this->mapKeys));

            throw new \UnexpectedValueException(sprintf('Has no described keys (%s)', implode(', ', $lostKeys)));
        }
    }
}

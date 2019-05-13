<?php

namespace AppBundle\Doctrine\ORM\Hydrator;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Inflector\Inflector;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use Symfony\Component\Validator\Constraints as Assert;

class DTOHydrator extends AbstractHydrator
{
    public const AVAILABLE_TYPES = ['string', 'integer', 'float', 'boolean'];

    /**
     * @var array
     */
    protected $mapKeys;

    /**
     * @var \ReflectionClass
     */
    protected $reflector;

    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData()
    {
        $result = [];

        while ($row = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($row, $result);
            if (DTORSM::OBJECT_SINGLE === $this->_rsm->getMode()) {
                return $result[0];
            }
        }

        if (empty($result) && DTORSM::OBJECT_SINGLE === $this->_rsm->getMode()) {
            return null;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
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
     * @param string|mixed $value
     * @param string       $type
     * @param string|null  $subtype
     *
     * @return bool|\DateTime|mixed|string|null
     *
     * @throws \Exception
     */
    protected function fetchValue($value, $type, $subtype)
    {
        if ('string' === $type) {
            return null === $value ? null : "$value";
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
                foreach ($array as $index => $val) {
                    $array[$index] = $this->fetchValue($val, $subtype, null);
                }
            }

            return $array;
        }

        throw new \UnexpectedValueException(sprintf('Can not hydrate value "%s".', $value));
    }

    /**
     * Mapping keys row to DTO properties.
     *
     * @param array $row
     *
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    protected function map($row): void
    {
        if (!$this->_rsm instanceof DTORSM) {
            throw new \InvalidArgumentException('ResultSetMapping must instance of DTORSM');
        }

        $snakeKeys = array_keys($row);
        $camelKeys = array_map(function ($key) {
            return Inflector::camelize($key);
        }, $snakeKeys);

        $reader = new AnnotationReader();
        $this->reflector = new \ReflectionClass($this->_rsm->getDTO());
        foreach ($this->reflector->getProperties() as $property) {
            $indexKey = array_search($property->getName(), $camelKeys);
            if (false === $indexKey) {
                continue;
            }
            $this->mapKeys[$snakeKeys[$indexKey]] = [
                'property' => $property->getName(),
                'type' => 'string',
                'subtype' => null,
            ];
            $annotations = $reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Assert\Date) {
                    $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'date';
                    continue;
                }
                if ($annotation instanceof Assert\DateTime) {
                    $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'datetime';
                    continue;
                }
                if ($annotation instanceof Assert\Type) {
                    if (in_array($annotation->type, self::AVAILABLE_TYPES)) {
                        $this->mapKeys[$snakeKeys[$indexKey]]['type'] = $annotation->type;
                    }
                    continue;
                }
                if ($annotation instanceof Assert\All) {
                    foreach ($annotation->constraints as $constraint) {
                        if ($annotation instanceof Assert\Date) {
                            $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'array';
                            $this->mapKeys[$snakeKeys[$indexKey]]['subtype'] = 'date';
                            continue;
                        }
                        if ($annotation instanceof Assert\DateTime) {
                            $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'array';
                            $this->mapKeys[$snakeKeys[$indexKey]]['subtype'] = 'datetime';
                            continue;
                        }
                        if ($annotation instanceof Assert\Type) {
                            if (in_array($annotation->type, self::AVAILABLE_TYPES)) {
                                $this->mapKeys[$snakeKeys[$indexKey]]['type'] = 'array';
                                $this->mapKeys[$snakeKeys[$indexKey]]['subtype'] = $annotation->type;
                            }
                            continue;
                        }
                    }
                }
            }
        }

        // var_dump($snakeKeys, $camelKeys, $this->mapKeys);exit;

        if (count($snakeKeys) !== count($this->mapKeys)) {
            $lostKeys = array_diff($snakeKeys, array_keys($this->mapKeys));

            throw new \UnexpectedValueException(sprintf('Has no described keys (%s)', implode(', ', $lostKeys)));
        }
    }
}

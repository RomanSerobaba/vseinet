<?php

namespace AppBundle\Bus\Message;

abstract class Message implements \IteratorAggregate
{
    /**
     * Initial object with values from iterable.
     *
     * @param iterable $values
     * @param iterable $extra
     */
    public function __construct(iterable $values = [], iterable $extra = [])
    {
        $iterator = new \AppendIterator();
        $iterator->append(new \ArrayIterator($values));
        $iterator->append(new \ArrayIterator($extra));
        foreach ($iterator as $property => $value) {
            if (property_exists($this, $property)) {
                $propertySetter = 'set'.ucfirst($property);
                if (method_exists($this, $propertySetter)) {
                    $this->$propertySetter($value);
                } else {
                    $this->$property = $value;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this);
    }
}

<?php 

namespace AppBundle\Bus\Message;

abstract class Message  
{
    /**
     * Constructor.
     *
     * @param array $values 
     * @param array $extra
     */
    public function __construct(array $values = [], array $extra = [])
    {
        $this->init($values, $extra);
    }

    /**
     * Initial object with values from array.
     *
     * @param array $values 
     * @param array $extra
     */
    public function init(array $values = [], array $extra = []) 
    {
        $data = $this->empty2null($extra + $values);

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }     
    }

    protected function from($object, array $keyMap = [])
    {
        $values = (array) $object;
        foreach ($keyMap as $original => $key) {
            $values[$key] = $object->$original;
        }
        $this->init($values);
    }

    protected function empty2null($value) 
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->empty2null($v);
            }

            return $value;
        }

        return is_string($value) && strlen($value) === 0 ? null : $value;
    }

    public function toArray()
    {
        $array = [];
        foreach ((array) $this as $key => $value) {
            if (null !== $value) {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    public function toArrayWith(array $keys)
    {
        $array = $this->toArray();
        foreach ($array as $key => $value) {
            if (!in_array($key, $keys)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function toArrayWithout(array $keys)
    {
        $array = $this->toArray();
        foreach ($array as $key => $value) {
            if (in_array($key, $keys)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
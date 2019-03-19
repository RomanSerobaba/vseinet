<?php

namespace AppBundle\Exception\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TraceError
{
    /**
     * @Assert\Type(type="string")
     */
    public $file;

    /**
     * @Assert\Type(type="integer")
     */
    public $line;

    /**
     * @Assert\Type(type="string")
     */
    public $function;

    /**
     * @Assert\Type(type="string")
     */
    public $class;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="array")
     */
    public $args;

    /**
     * @param array $trace
     */
    public function __construct(array $trace)
    {
        if (!empty($trace['file'])) {
            $this->file = substr($trace['file'], strpos($trace['file'], '/vseinet.ru/') + 12);
        }
        if (!empty($trace['line'])) {
            $this->line = $trace['line'];
        }
        $this->function = $trace['function'];
        if (!empty($trace['class'])) {
            $this->class = $trace['class'];
        }
        if (!empty($trace['type'])) {
            $this->type = $trace['type'];
        }
        $this->args = $trace['args'];
    }
}

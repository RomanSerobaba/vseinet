<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DocItemException extends BadRequestHttpException
{
    /**
     * @var mixed
     */
    protected $key;

    /**
     * @param mixed           $key
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct($key, string $message, \Exception $previous = null)
    {
        $this->key = $key;

        parent::__construct($message, $previous);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @var mixed
     *
     * @return self
     */
    public function setKey($key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return [$this->getKey() => $this->getMessage()];
    }
}

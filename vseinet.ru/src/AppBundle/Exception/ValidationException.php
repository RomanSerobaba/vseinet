<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
{
    /**
     * @var string
     */
    protected $parameter;

    /**
     * @param string          $parameter
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct(string $parameter, string $message, \Exception $previous = null)
    {
        $this->parameter = $parameter;

        parent::__construct($message, $previous);
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @var string
     *
     * @return self
     */
    public function setParameter(string $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return [$this->getParameter() => $this->getMessage()];
    }
}

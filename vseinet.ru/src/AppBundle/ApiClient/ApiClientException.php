<?php

namespace AppBundle\ApiClient;

use Symfony\Component\HttpKernel\Exception;

class ApiClientException extends Exception\HttpException
{
    private $paramErrors;
    private $debugTokenLink;

    /**
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     * @param array      $paramErrors   Form errors list
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0, array $paramErrors = [], string $debugTokenLink = '')
    {
        $this->debugTokenLink = $debugTokenLink;
        $this->paramErrors = $paramErrors;
        parent::__construct(400, $message, $previous, [], $code);
    }

    public function getParamErrors()
    {
        return $this->paramErrors;
    }

    public function getDebugTokenLink()
    {
        return $this->debugTokenLink;
    }
}

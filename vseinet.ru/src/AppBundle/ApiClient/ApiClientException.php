<?php

namespace AppBundle\ApiClient;

use Symfony\Component\HttpKernel\Exception;

class ApiClientException extends Exception\HttpException
{
    private $debugTokenLink;

    /**
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0, string $debugTokenLink = '')
    {
        $this->debugTokenLink = $debugTokenLink;
        parent::__construct(400, $message, $previous, [], $code);
    }

    public function getDebugTokenLink()
    {
        return $this->debugTokenLink;
    }
}

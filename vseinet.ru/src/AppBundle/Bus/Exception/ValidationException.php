<?php 

namespace AppBundle\Bus\Exception; 

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
{
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $messages, $code = 0, \Exception $previous = null)
    {
        $this->messages = $messages;

        parent::__construct(count($messages) ? reset($this->messages) : '', $previous, $code);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
<?php

namespace AppBundle\Exception\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ResponseError
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Код ошибки")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Сообщение ошибки")
     */
    public $message;

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Exception\DTO\ParameterError"))
     * @VIA\Description("Ошибки валидации входящих параметров")
     */
    public $parameters = [];

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Exception\DTO\DocItemError"))
     * @VIA\Description("Ошибки табличной части документа")
     */
    public $docItems = [];

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Exception\DTO\TraceError"))
     * @VIA\Description("Трассировка стека")
     */
    public $trace;

    /**
     * @param int    $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @param int $code
     *
     * @return self
     */
    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $parameter
     * @param string $message
     *
     * @return self
     */
    public function addParameterError(string $parameter, string $message): self
    {
        $this->parameters[] = new ParameterError($parameter, $message);

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixin  $key
     * @param string $message
     *
     * @return self
     */
    public function addDocItemError($key, string $message): self
    {
        $this->docItems[] = new DocItemError($key, $message);

        return $this;
    }

    /**
     * @return array
     */
    public function getDocItems(): array
    {
        return $this->docItems;
    }

    /**
     * @param array $trace
     *
     * @return self
     */
    public function setTrace(array $trace): self
    {
        $this->trace = [];
        foreach ($trace as $t) {
            $this->trace[] = new TraceError($t);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTrace()
    {
        return $this->trace;
    }
}

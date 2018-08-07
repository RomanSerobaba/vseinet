<?php 

namespace AppBundle\Bus\User\Command\Validator;

use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Enum\ContactTypeCode;

class ContactValidator
{
    protected $typeCode;

    protected $value;

    public function __construct($typeCode, $value)
    {
        $this->typeCode = $typeCode;
        $this->value = $value;
    }

    public function validate()
    {
        if (ContactTypeCode::MOBILE === $this->typeCode || ContactTypeCode::PHONE === $this->typeCode) {
            $this->value = preg_replace('/\D+/', '', $this->value);
            if (11 === strlen($this->value) && ('7' === $this->value[0] || '8' === $this->value)) {
                $this->value = substr($this->value);
            }
        }
        if (ContactTypeCode::MOBILE === $this->typeCode) {
            if (10 !== strlen($this->value) || '9' !== $this->value[0]) {
                throw new ValidationException([
                    'value' => 'Неверный формат мобильного номера телефона',
                ]);
            }
        }
        if (ContactTypeCode::PHONE === $this->typeCode) {
            if (!in_array(strlen($this->value), [6, 7, 10])) {
                throw new ValidationException([
                    'value' => 'Неверный формат телефона',
                ]);
            }
        }
        if (ContactTypeCode::EMAIL === $this->typeCode) {
            if (false === strpos($this->value, '@')) {
                throw new ValidationException([
                    'value' => 'Неверный формат email',
                ]);
            }
        }
    }

    public function getValue()
    {
        return $this->value;
    }
}

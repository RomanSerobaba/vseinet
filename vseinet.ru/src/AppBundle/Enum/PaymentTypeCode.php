<?php

namespace AppBundle\Enum;

class PaymentTypeCode
{
    const CASH = 'cash';
    const CREDIT = 'credit';
    const CASHLESS = 'cashless';
    const WEBMONEY = 'webmoney';
    const BANKCARD = 'bankcard';
    const TERMINAL = 'terminal';
    const SBERBANK = 'sberbank';
    const INSTALLMENT = 'installment';

    const INSTALLMENT_PERCENT = 8;

    public static function getChoices(): array
    {
        return [
            self::CASH => 'Наличными',
            self::CREDIT => 'Кредитом',
            self::CASHLESS => 'На рассчетный счет (по реквизитам организации)',
            self::WEBMONEY => 'Webmoney',
            self::BANKCARD => 'Переводом на банковскую карту',
            self::TERMINAL => 'Через терминал',
            self::SBERBANK => 'Через Сбербанк',
            self::INSTALLMENT => 'Рассрочка',
        ];
    }

    public static function getName(string $code): string
    {
        $choices = self::getChoices();

        if (!isset($choices[$code])) {
            throw new \LoginException(sprintf('Choice "%s" in class "%" not found.', $code, get_called_class()));
        }

        return $choices[$code];
    }
}

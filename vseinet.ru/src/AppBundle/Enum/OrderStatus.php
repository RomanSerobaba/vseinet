<?php 

class OrderStatus
{
    const CREATED = 'created';
    const LACK = 'lack';
    const PREPAYABLE = 'prepayable';
    const CALLABLE = 'callable';
    const SHIPPING = 'shipping';
    const TRANSIT = 'transit';
    const STATIONED = 'stationed';
    const TRANSPORT = 'transport';
    const POST = 'post';
    const COURIER = 'courier';
    const RELEASABLE = 'releasable';
    const ARRIVED = 'arrived';
    const ANNULLED = 'annulled';
    const CANCELED = 'canceled';
    const COMPLETED = 'completed';
    const ISSUED = 'issued';
    const REFUNDED = 'refunded';

    public static function getChoices(): array
    {
        return [
            self::CREATED => 'обработан',
            self::LACK => 'обработан, нет в наличии',
            self::PREPAYABLE => 'обработан, ожидается предоплата',
            self::CALLABLE => 'зарезервирован',
            self::SHIPPING => 'в пути',
            self::TRANSIT => 'в пути',
            self::STATIONED => 'в пути',
            self::TRANSPORT => 'на доставке перевозчиком',
            self::POST => 'на доставке почтой',
            self::COURIER => 'на доставке курьером',
            self::RELEASABLE => 'готов к получению',
            self::ARRIVED = 'готов к получению',
            self::COMPLETED => 'выполнен',
            self::ANNULLED => 'аннулирован',
            self::CANCELED => 'отменен',
            self::ISSUED => 'в сервисном отделе',
            self::REFUNDED => 'возврат средств',
        ];
    }

    public static function getTitle(string $status): string
    {
        $choices = self::getChoices();
        if (!isset($choices[$status])) {
            throw new \LogicException(sprintf('Неверный статус заказа "%s"', $status));
        }

        return $choices[$status];
    }
}

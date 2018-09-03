<?php 

namespace AppBundle\Enum;

class OrderItemStatus
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
            self::CREATED => 'обрабатывается',
            self::LACK => 'обработан, нет в наличии',
            self::PREPAYABLE => 'обработан, ожидается предоплата',
            self::CALLABLE => 'обработан, зарезервирован',
            self::SHIPPING => 'отгружается',
            self::TRANSIT => 'в пути',
            self::STATIONED => 'комплектуется',
            self::TRANSPORT => 'доставка перевозчиком',
            self::POST => 'доставка почтой',
            self::COURIER => 'доставка курьером',
            self::RELEASABLE => 'готов к получению',
            self::ARRIVED => 'готов к получению',
            self::COMPLETED => 'выполнен',
            self::ANNULLED => 'аннулирован',
            self::CANCELED => 'отменен',
            self::ISSUED => 'в сервисном отделе',
            self::REFUNDED => 'возврат средств',
        ];
    }

    public static function getName(string $status): string
    {
        $choices = self::getChoices();
        if (!isset($choices[$status])) {
            throw new \LogicException(sprintf('Неверный статус заказа "%s"', $status));
        }

        return $choices[$status];
    }

    public static function getTracker(string $status): array
    {
        switch ($status) {
            case self::LACK:
                return [
                    self::CREATED => true,
                    self::LACK => true,
                ];

            case self::PREPAYABLE:
                return [
                    self::CREATED => true,
                    self::PREPAYABLE => true,
                    self::SHIPPING => false,
                    self::TRANSIT => false,
                    self::RELEASABLE => false,
                    self::COMPLETED => false,
                ];

            case self::CALLABLE:
            case self::SHIPPING:
            case self::TRANSIT:
                return [
                    self::CREATED => true,
                    self::CALLABLE => true,
                    self::SHIPPING => self::SHIPPING === $status || self::TRANSIT == $status,
                    self::TRANSIT => self::TRANSIT === $status,
                    self::RELEASABLE => false,
                    self::COMPLETED => false,
                ];

            case self::STATIONED:
                return [
                    self::CREATED => true,
                    self::CALLABLE => true,
                    self::STATIONED => true,
                    self::TRANSIT => false,
                    self::RELEASABLE => false,
                    self::COMPLETED => false,
                ];

            case self::TRANSPORT:
            case self::POST:
            case self::COURIER:
                return [
                    self::CREATED => true,
                    self::CALLABLE => true,
                    $status => true,
                    self::RELEASABLE => false,
                    self::COMPLETED => false,
                ];    

            default: 
                return [
                    $status => true,
                ];
        }
    }
}

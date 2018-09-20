<?php 

namespace AppBundle\Enum;

class ProductToCompetitorStatus
{
    const ADDED = 'added';
    const REQSUETED = 'requested';
    const COMPLETED = 'completed';

    public static function getChoices()
    {
        return [
            self::ADDED => 'Добавлено',
            self::REQUESTED => 'Запрошено',
            self::COMPLETED => 'Выполнено',
        ];
    }
}

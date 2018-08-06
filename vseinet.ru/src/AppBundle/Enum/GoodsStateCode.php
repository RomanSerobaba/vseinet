<?php 

namespace AppBundle\Enum;

class GoodsStateCode
{
    /**
     * Нет дефектов (не дефект)
     */
    const NORMAL = 'normal';
    /**
     * Некомплект
     */
    const INCOMPLETE = 'incomplete';
    /**
     * Некомплект
     */
    const RECONSORT = 'reconsort';
    /**
     * Перегруз (не дефект)
     */
    const OVERLOAD = 'overload';
    /**
     * Недогруз (не дефект)
     */
    const UNDERLOAD = 'underload';
    /**
     * Повреждение товара
     */
    const BROKEN = 'broken';
    /**
     * Брак
     */
    const DEFECTIVE = 'defective';
    /**
     *
     */
    const DISAPPERATED = 'disappeared';
    /**
     * Найден/Излишек (не дефект)
     */
    const FOUND = 'found';
    /**
     * Не найден/Недостача (не дефект)
     */
    const ARRANGED = 'not arranged';
    /**
     * Повреждение упаковки
     */
    const PACKAGING_DEFECT = 'packaging defect';
    /**
     * Отложенная выдача (не дефект)
     */
    const IS_WAITING = 'is waiting';

}

<?php 

namespace AppBundle\Enum;

class GoodsPalletStatusType
{
    /** 
     * Свободная паллета
     */
    const FREE = 'free';
    /**
     * Открытая/активная паллета
     */
    const OPENED = 'opened';
    /**
     * Закрытая/не активная паллета на складе (до отправки)
     */
    const CLOSED = 'closed';
    /**
     * Паллета в пути
     */
    const IN_WAY = 'in_way';
    /**
     * Списанная паллета (полсе разбора)
     */
    const WRITE_OFF = 'write_off';

}
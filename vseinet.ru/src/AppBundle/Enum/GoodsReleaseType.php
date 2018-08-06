<?php 

namespace AppBundle\Enum;

class GoodsReleaseType
{
    /** 
     * Отпуск товара клиенту
     */
    const CLIENT = 'client';
    /**
     * Отгрузка в другое подразделение
     */
    const TRANSIT = 'transit';
    /**
     * Отгрузка внутри подразделения
     */
    const MOVEMENT = 'movement';
    /**
     * Отгрузка на ремонт
     */
    const ISSUE = 'issue';
    /**
     * Отгрузка в транспортную компанию
     */
    const FREIGHT = 'freight';
    /**
     * Отгрузка курьеру
     */
    const COURIER = 'courier';

}
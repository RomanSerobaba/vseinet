<?php

namespace ServiceBundle\Components;


use ServiceBundle\Components\smarty\SmartyModifierWrittenNumber;

class Number
{
    /**
     * вычисление порядка числа
     *
     * @param float $value
     * @param float $e - точность
     *
     * @return float
     */
    public static function calcExp($value, $e = -4)
    {
        $value = round($value / pow(10, $e));
        foreach (str_split(strrev($value)) as $char) {
            if ($char == '0') {
                $e++;
            } else {
                break;
            }
        }

        return $e;
    }

    /**
     * @param      $number
     * @param null $decimals
     *
     * @return string
     */
    public static function format($number, $decimals = null)
    {
        $r = floor($number / 100);
        $price = number_format($r, 0, '.', ' ');

        if ($hide_zero_k = null === $decimals) {
            $decimals = 2;
        }
        if (1 == $decimals || 2 == $decimals) {
            $k = abs($number % 100);
            if ($k || !$hide_zero_k) {
                if (1 == $decimals) {
                    $k = round($k / 10);
                }
                $price .= '.' . sprintf("%'.0{$decimals}d", $k);
            }
        }

        return $price;
    }

    /**
     * @param $value
     *
     * @return float
     */
    public static function input($value)
    {
        return round(100 * floatval(str_replace([' ', ','], ['', '.'], $value)));
    }

    /**
     * @param $value
     *
     * @return integer
     */
    public static function formatPrice($value) : int
    {
        return (int) 100 * floatval($value);
    }

    /**
     * @param      $number
     * @param null $decimals
     *
     * @return string
     */
    public static function formatInvoice($number, $decimals = null)
    {
        $r = $number / 100;
        return number_format($r, 2, '.', ' ');
    }

    /**
     * @param $price
     *
     * @return float
     */
    public static function price2Float($price)
    {
        return round($price / 100, 2);
    }

    /**
     * @param $number
     *
     * @return string
     */
    public static function toStr($number)
    {
        list($r, $k) = explode('.', static::format($number, 2));
        $r = intval(str_replace(' ', '', $r));
        $number = new SmartyModifierWrittenNumber();

        return $number->format($r) . ' руб. ' . $k . ' коп.';
    }

    /**
     * @param $number
     *
     * @return bool|mixed|string
     */
    public static function toStrShort($number)
    {
        list($r, $k) = explode('.', static::format($number, 2));
        $r = intval(str_replace(' ', '', $r));
        $number = new SmartyModifierWrittenNumber();

        return $number->format($r);
    }

    /**
     * @param     $param
     * @param int $time
     *
     * @return false|string
     */
    public static function rusMonthDate($param, $time = 0)
    {
        if (intval($time) == 0) {
            $time = time();
        }

        $monthNames = [
            "января",
            "февраля",
            "марта",
            "апреля",
            "мая",
            "июня",
            "июля",
            "августа",
            "сентября",
            "октября",
            "ноября",
            "декабря"
        ];

        if (strpos($param, 'M') === false) {
            return date($param, $time);
        } else {
            return date(str_replace('M', $monthNames[date('n', $time) - 1], $param), $time);
        }
    }
}
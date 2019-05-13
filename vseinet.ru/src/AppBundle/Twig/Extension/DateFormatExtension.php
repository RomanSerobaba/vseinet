<?php

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

setlocale(LC_ALL, 'ru_RU.UTF-8');

class DateFormatExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('date_format', [$this, 'format']),
        ];
    }

    public function format($date, $format)
    {
        if (empty($date)) {
            $timestamp = time();
        } elseif ($date instanceof \DateTimeInterface) {
            $timestamp = $date->format('U');
        } elseif (is_numeric($date)) {
            $timestamp = intval($date);
        } else {
            $timestamp = strtotime($date);
        }

        return strftime($format, $timestamp);
    }
}

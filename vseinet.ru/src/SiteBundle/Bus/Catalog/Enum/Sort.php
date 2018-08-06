<?php 

namespace SiteBundle\Bus\Catalog\Enum;

class Sort
{
    const DEFAULT = 'default';
    const MARGING = 'marging';
    const RATING = 'rating';
    const PRICE = 'price';
    const NOVELTY = 'novelty';
    const NAME = 'name';

    public static function getOptions(bool $isEmployee): array 
    {
        $options = [
            self::RATING => 'популярности',
            self::PRICE => 'цене',
            self::NOVELTY => 'новизне',
            self::NAME => 'наименованию',
        ];
        if ($isEmployee) {
            $options = [self::MARGING => 'лучшему'] + $options;
        }

        return $options;
    }
}

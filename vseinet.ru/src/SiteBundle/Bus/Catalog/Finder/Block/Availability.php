<?php 

namespace SiteBundle\Bus\Catalog\Finder\Block;

class Availability
{
    public static function build(array $availability2count)
    {
        ksort($availability2count);
        $acc = 0;
        foreach ($availability2count as $index => $count) {
            $availability2count[$index] = $acc += $count;
        }

        return $availability2count;
    }
}

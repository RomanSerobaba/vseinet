<?php

namespace AppBundle\Service;

use AppBundle\Container\ContainerAware;

class Transliterator extends ContainerAware
{
    private $baseMap = [
        'а' => 'a', 'б' => 'b', 'в' => 'v',
        'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'yo','ж' => 'zh','з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'kh','ц' => 'ts',
        'ч' => 'ch','ш' => 'sh','щ' => 'sch',
        'ъ' => '',  'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu','я' => 'ya',

        'А' => 'A', 'Б' => 'B', 'В' => 'V',
        'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'Yo','Ж' => 'Zh','З' => 'Z',
        'И' => 'I', 'Й' => 'Y', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U',
        'Ф' => 'F', 'Х' => 'Kh','Ц' => 'Ts',
        'Ч' => 'Ch','Ш' => 'Sh','Щ' => 'Sch',
        'Ъ' => '',  'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu','Я' => 'Ya',
    ];

    private $extendedMap = [
        '[\s_-]+' => '-',

        'ъе' => 'ye', 'ый' => 'iy', 'ий' => 'iy',

        'Ъе' => 'Ye', 'Ый' => 'Iy', 'Ий' => 'Iy',
    ];

    public function toTranslit(string $string): string
    {
        $prepareString = trim(preg_replace('~[^ая-яА-Яa-zA-Z\d-\s_]+~isu', '', trim($string)));

        return preg_replace($this->patternFromMap($this->baseMap), $this->baseMap, preg_replace($this->patternFromMap($this->extendedMap), $this->extendedMap, $prepareString));
    }

    private function patternFromMap($map)
    {
        return array_map(function($e) { return '/'.$e.'/isu'; }, array_keys($map));
    }
}

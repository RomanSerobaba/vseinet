<?php

namespace ServiceBundle\Components;


class Utils
{
    /**
     * @param string $str
     * @param bool   $isAlias
     *
     * @return string
     */
    public static function translitIt(string $str, $isAlias = false): string
    {
        $tr = array(
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Е" => "E",
            "Ё" => "Jo",
            "Ж" => "Zh",
            "З" => "Z",
            "И" => "I",
            "Й" => "J",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "H",
            "Ц" => "C",
            "Ч" => "Ch",
            "Ш" => "Sh",
            "Щ" => "Shh",
            "Ъ" => "##",
            "Ы" => "Yi",
            "Ь" => "''",
            "Э" => "Je",
            "Ю" => "Ju",
            "Я" => "Ja",
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ё" => "jo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "j",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "h",
            "ц" => "c",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "shh",
            "ъ" => "#",
            "ы" => "yi",
            "ь" => "'",
            "э" => "je",
            "ю" => "ju",
            "я" => "ja"
        );
        $clean = strtr($str, $tr);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -\.]/", '', $clean);
        if (!$isAlias) {
            $clean = strtolower(trim($clean, '-'));
            $clean = preg_replace("/[\/|+ -]+/", "_", $clean);
        } else {
            $clean = mb_convert_case(trim($clean, '-'), MB_CASE_TITLE, "UTF-8");
            $clean = preg_replace("/[\/|+ -]+/", '', $clean);
        }

        return $clean;
    }

    /**
     * @param string $str
     * @param string $encoding
     *
     * @return string
     */
    public static function mbUcaseFirst(string $str, string $encoding): string
    {
        mb_regex_encoding("UTF-8");
        $str = mb_ereg_replace('^\w{1}', mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding), $str);

        return $str;
    }

    /**
     * @param string $fullname
     *
     * @return string
     */
    public static function getShortName(string $fullname) : string
    {
        if (empty($fullname)) {
            return '';
        }

        $shortNames = [];
        $names = explode(' ', $fullname);

        $shortNames[] = $names[0];
        if (!empty($names[1])) {
            $shortNames[] = mb_strtoupper(mb_substr($names[1], 0, 1)) . '.';
        }
        if (!empty($names[2])) {
            $shortNames[] = mb_strtoupper(mb_substr($names[2], 0, 1)) . '.';
        }

        return implode(' ', $shortNames);
    }

    /**
     * @param $number
     *
     * @return string
     */
    public static function number2string($number): string
    {
        static $dic = array(

            array(
                -2 => 'две',
                -1 => 'одна',
                1 => 'один',
                2 => 'два',
                3 => 'три',
                4 => 'четыре',
                5 => 'пять',
                6 => 'шесть',
                7 => 'семь',
                8 => 'восемь',
                9 => 'девять',
                10 => 'десять',
                11 => 'одиннадцать',
                12 => 'двенадцать',
                13 => 'тринадцать',
                14 => 'четырнадцать',
                15 => 'пятнадцать',
                16 => 'шестнадцать',
                17 => 'семнадцать',
                18 => 'восемнадцать',
                19 => 'девятнадцать',
                20 => 'двадцать',
                30 => 'тридцать',
                40 => 'сорок',
                50 => 'пятьдесят',
                60 => 'шестьдесят',
                70 => 'семьдесят',
                80 => 'восемьдесят',
                90 => 'девяносто',
                100 => 'сто',
                200 => 'двести',
                300 => 'триста',
                400 => 'четыреста',
                500 => 'пятьсот',
                600 => 'шестьсот',
                700 => 'семьсот',
                800 => 'восемьсот',
                900 => 'девятьсот'
            ),

            array(
                array('рубль', 'рубля', 'рублей'),
                array('тысяча', 'тысячи', 'тысяч'),
                array('миллион', 'миллиона', 'миллионов'),
                array('миллиард', 'миллиарда', 'миллиардов'),
                array('триллион', 'триллиона', 'триллионов'),
                array('квадриллион', 'квадриллиона', 'квадриллионов'),
            ),

            array(
                2,
                0,
                1,
                1,
                1,
                2
            )
        );

        $string = array();

        $number = str_pad($number, ceil(strlen($number) / 3) * 3, 0, STR_PAD_LEFT);

        $parts = array_reverse(str_split($number, 3));

        foreach ($parts as $i => $part) {

            if ($part > 0) {

                $digits = array();

                if ($part > 99) {
                    $digits[] = floor($part / 100) * 100;
                }

                if ($mod1 = $part % 100) {
                    $mod2 = $part % 10;
                    $flag = $i == 1 && $mod1 != 11 && $mod1 != 12 && $mod2 < 3 ? -1 : 1;
                    if ($mod1 < 20 || !$mod2) {
                        $digits[] = $flag * $mod1;
                    } else {
                        $digits[] = floor($mod1 / 10) * 10;
                        $digits[] = $flag * $mod2;
                    }
                }

                $last = abs(end($digits));

                foreach ($digits as $j => $digit) {
                    $digits[$j] = $dic[0][$digit];
                }

                $digits[] = $dic[1][$i][(($last %= 100) > 4 && $last < 20) ? 2 : $dic[2][min($last % 10, 5)]];
                array_unshift($string, join(' ', $digits));
            }
        }

        $final_string = join(' ', $string);

        if ($parts[0] == '000') {
            $final_string .= ' рублей';
        }

        return $final_string;
    }

    /**
     * @param string $param
     * @param int    $time
     *
     * @return string
     */
    public static function rusMonthDate(string $param, $time = 0): string
    {
        if (intval($time) == 0) {
            $time = time();
        }
        $MonthNames = array(
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
        );
        if (strpos($param, 'M') === false) {
            return date($param, $time);
        } else {
            return date(str_replace('M', $MonthNames[date('n', $time) - 1], $param), $time);
        }
    }

    /**
     * @param string $text
     * @param int    $arrow
     *
     * @return string
     */
    public static function switcher(string $text, $arrow = 0) : string
    {
        $str[0] = array(
            'й' => 'q',
            'ц' => 'w',
            'у' => 'e',
            'к' => 'r',
            'е' => 't',
            'н' => 'y',
            'г' => 'u',
            'ш' => 'i',
            'щ' => 'o',
            'з' => 'p',
            'х' => '[',
            'ъ' => ']',
            'ф' => 'a',
            'ы' => 's',
            'в' => 'd',
            'а' => 'f',
            'п' => 'g',
            'р' => 'h',
            'о' => 'j',
            'л' => 'k',
            'д' => 'l',
            'ж' => ';',
            'э' => '\'',
            'я' => 'z',
            'ч' => 'x',
            'с' => 'c',
            'м' => 'v',
            'и' => 'b',
            'т' => 'n',
            'ь' => 'm',
            'б' => ',',
            'ю' => '.',
            'Й' => 'Q',
            'Ц' => 'W',
            'У' => 'E',
            'К' => 'R',
            'Е' => 'T',
            'Н' => 'Y',
            'Г' => 'U',
            'Ш' => 'I',
            'Щ' => 'O',
            'З' => 'P',
            'Х' => '[',
            'Ъ' => ']',
            'Ф' => 'A',
            'Ы' => 'S',
            'В' => 'D',
            'А' => 'F',
            'П' => 'G',
            'Р' => 'H',
            'О' => 'J',
            'Л' => 'K',
            'Д' => 'L',
            'Ж' => ';',
            'Э' => '\'',
            '?' => 'Z',
            'ч' => 'X',
            'С' => 'C',
            'М' => 'V',
            'И' => 'B',
            'Т' => 'N',
            'Ь' => 'M',
            'Б' => ',',
            'Ю' => '.',
        );
        $str[1] = array(
            'q' => 'й',
            'w' => 'ц',
            'e' => 'у',
            'r' => 'к',
            't' => 'е',
            'y' => 'н',
            'u' => 'г',
            'i' => 'ш',
            'o' => 'щ',
            'p' => 'з',
            '[' => 'х',
            ']' => 'ъ',
            'a' => 'ф',
            's' => 'ы',
            'd' => 'в',
            'f' => 'а',
            'g' => 'п',
            'h' => 'р',
            'j' => 'о',
            'k' => 'л',
            'l' => 'д',
            ';' => 'ж',
            '\'' => 'э',
            'z' => 'я',
            'x' => 'ч',
            'c' => 'с',
            'v' => 'м',
            'b' => 'и',
            'n' => 'т',
            'm' => 'ь',
            ',' => 'б',
            '.' => 'ю',
            'Q' => 'Й',
            'W' => 'Ц',
            'E' => 'У',
            'R' => 'К',
            'T' => 'Е',
            'Y' => 'Н',
            'U' => 'Г',
            'I' => 'Ш',
            'O' => 'Щ',
            'P' => 'З',
            '[' => 'Х',
            ']' => 'Ъ',
            'A' => 'Ф',
            'S' => 'Ы',
            'D' => 'В',
            'F' => 'А',
            'G' => 'П',
            'H' => 'Р',
            'J' => 'О',
            'K' => 'Л',
            'L' => 'Д',
            ';' => 'Ж',
            '\'' => 'Э',
            'Z' => '?',
            'X' => 'ч',
            'C' => 'С',
            'V' => 'М',
            'B' => 'И',
            'N' => 'Т',
            'M' => 'Ь',
            ',' => 'Б',
            '.' => 'Ю',
        );

        return strtr($text, isset($str[$arrow]) ? $str[$arrow] : array_merge($str[0], $str[1]));
    }
}

<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MCStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category' => 'группа',
                    'article' => 'артикул',
                    'name' => 'наименование товара',
                    'price' => 'цена, опт',
                    'availability' => 'наличие / транзит',
                    'price_retail_min' => 'ррц',
                ],
            ],
        ];
    }

    const TYPE = 'Элементы питания, зарядники';

    const EXCLUDE = [
        'GPS-навигаторы',
        'Эхолоты',
        'Автосигнализации',
        'Видеорегистраторы автомобильные',
        'Радар-детекторы',
    ];

    const PATTERN = '/(Аккумулятор.*?)/si';

    const BATTERY_END = ' (цена за 1 шт.)';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (!in_array($data['category'], self::EXCLUDE)) {
            $data['price_retail_min'] = 0;
        }
        $p = mb_strpos($data['name'], 'РРЦ');
        if (false !== $p) {
            $data['name'] = trim(mb_substr($data['name'], 0, $p - 1), ' ,');
            $data['code'] = $data['article'];
        }
        $data['name'] = str_replace('&#38;#34;', '"', $data['name']);

        if ('+' == $data['availability']) {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }
        else {
            $data['availability'] = ProductAvailabilityCode::IN_TRANSIT;
        }

        if ($data['category'] == self::TYPE) {
            if (preg_match_all(self::PATTERN, $data['name'], $matches)) {
                $data['name'] .= self::BATTERY_END;
            }
        }

        $data['categories'][] = $data['category'];

        return $data;
    }
}
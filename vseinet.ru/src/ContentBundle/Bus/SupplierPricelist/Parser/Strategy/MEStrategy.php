<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MEStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category1' => 'группа 1',
                    'category2' => 'группа 2',
                    'category3' => 'группа 3',
                    'brand' => 'бренд',
                    'code' => 'номер',
                    'article' => 'код производителя',
                    'name' => 'наименование',
                    'price' => 'цена(руб)',
                    'availability1' => 'доступно',
                    'availability2' => 'ожидаемый приход',
                    'availability3' => 'следующий приход',
                    'min_quantity' => 'мин. партия покупки',
                ],
            ],
        ];
    }

    const EXCLUDE = [
        'ПЛОХАЯ УПАКОВКА',
        'ВОССТАНОВЛЕННЫЙ',
    ];

    const PATTERN = '/(Аккумулятор.*?)\((.*?)шт\)/i';

    const BATTERY_END = ' (цена за 1 шт.)';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (in_array($data['category1'], self::EXCLUDE)) {
            return null;
        }

        if (!$data['availability1'] || 'call' == $data['availability1']) {
            if (substr_count($data['availability2'], "+") || substr_count($data['availability3'], "+")) {
                $data['availability'] = ProductAvailabilityCode::IN_TRANSIT;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
            }
        }
        else {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }
        $data['coefficient_price'] = 0.95;

        if (preg_match_all(self::PATTERN, $data['name'], $matches)) {
            $data['name'] = trim($matches[1][0]).self::BATTERY_END;
            $count = intval($matches[2][0]);
            if ($count > 1) {
                $data['price'] = $data['price'] / $count;
            }
        }

        $data['categories'] = [
            $data['category1'],
            $data['category2'],
            $data['category3'],
        ];

        return $data;
    }
}
<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CITStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category1' => 'группа',
                    'category2' => 'подгруппа',
                    'brand' => 'бренд',
                    'code' => 'номер',
                    'article' => 'код производителя',
                    'name' => 'наименование',
                    'price' => 'цена опт',
                    'competitor_price' => 'цена vip',
                    'availability' => 'доступно',
                ],
            ],
        ];
    }

    const PATTERN1 = '/(Аккумулятор)(.*?),[\s+]*(\d*?) шт.(.*)/i';
    const PATTERN2 = '/(зарядное|устройство)/si';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['availability'] && 'СИТИЛИНК' != $data['brand']) {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }
        else {
            $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
        }

        $data['categories'] = [
            $data['category1'],
            $data['category2'],
        ];

        if (preg_match_all(self::PATTERN1, $data['name'], $matches1)) {
            $end = trim($matches1[2][0]);

            if (preg_match_all(self::PATTERN2, $end, $matches2)) {
                if (!empty($matches2)) {
                    return $data;
                }
            }

            $data['name'] = trim($matches1[1][0].' '.$end).', '.trim($matches1[4][0]).' (цена за 1 шт.)';

            $count = intval($matches1[3][0]);

            if ($count > 1) {
                $data['price'] = $data['price'] / $count;
            }
        }

        return $data;
    }
}
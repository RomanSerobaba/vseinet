<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PLStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;

    protected $isKeepCategories = true;

    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'id',
                    'category' => 'каталог',
                    'name' => 'наименование',
                    'price' => 'цена',
                    'url' => 'ссылка',
                ],
            ],
        ];
    }

    const BATTERY_END = ' (цена за 1 шт.)';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['category']) {
            $data['categories'][] = $data['category'];
        }
        else {
            $p = mb_strpos($data['name'], ' ', 0, 'UTF-8');
            $data['categories'] = [
                'Товары',
                mb_substr($data['name'], 0, $p, 'UTF-8'),
            ];
        }

        $data['url'] = str_replace('http://pleer.ru//', 'http://pleer.ru/product', $data['url']);
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;
     
        $pattern = '/(AA|АА)(.*)\((.*?) штук.*?\)(.*)/si';

        $pos = strpos($data['name'], 'AA+AAA');
        if ($pos !== false) {
            return $data;
        }

        if (preg_match_all($pattern, $data['name'], $matches)) {
            $end = isset($matches[4][0]) ? trim($matches[4][0]) : '';
            $data['name'] = trim($matches[1][0]);
            $data['name'] .= $matches[2][0];
            if (!empty($end)) {
                $data['name'] .= ' ' . $end;
            }
            $data['name'] .= self::BATTERY_END;
            $count = intval($matches[3][0]);

            if ($count > 1) {
                $data['price'] = $data['price'] / $count;
            }
        }

        return $data;
    }
}
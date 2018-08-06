<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OLTStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category1' => '№ п/п',
                    'category2' => 'категория',
                    'brand' => 'производитель',
                    'code' => 'артикул',
                    'name' => 'наименование',
                    'price' => 'цена',
                    'availability' => 'свободно',
                    'bar_code' => 'ean13',
                ],
            ],
        ];
    }

    protected $category1;

    const PATTERN = '/(Аккумулятор.*?)\(уп (.*?) шт\)(.*)/i';

    const BATTERY_END = ' (цена за 1 шт.)';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['name']) {
            $data['model'] = $data['code'];
            $data['categories'] = [
                $this->category1,
                $data['category2'],
            ];
            if ($data['availability']) {
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
            }

            $style = $sheet->getStyleByColumnAndRow($fields['code'], $row);
            if (preg_match('~^0{2,}$~isu', $formatCode = $style->getNumberFormat()->getFormatCode())) {
                $data['code'] = str_pad($data['code'], strlen($formatCode), "0", STR_PAD_LEFT);
            }

            if (preg_match_all(self::PATTERN, $data['name'], $matches)) {
                $end = isset($matches[3][0]) ? trim($matches[3][0]) : '';
                $data['name'] = trim($matches[1][0]);
                if (!empty($end)) {
                    $data['name'] .= ' '.$end;
                }
                $data['name'] .= self::BATTERY_END;
                $count = intval($matches[2][0]);

                if ($count > 1) {
                    $data['price'] = $data['price'] / $count;
                }
            }

            $data['bar_codes'] = array_map('trim', explode(',', $data['bar_code']));
        
            return $data;
        }
        
        $this->category1 = $data['category1'];

        return null;
    }
}
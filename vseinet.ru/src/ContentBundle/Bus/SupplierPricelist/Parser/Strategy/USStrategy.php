<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class USStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'number' => '№',
                    'name' => 'товар',
                    'price' => 'цена',
                ],
            ],
        ];
    }

    protected $categories = [];

    protected $brand;

    const BATTERY_END = ' (цена за 1 шт.)';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if(empty($data['number'])) { 
            $font = $sheet->getStyleByColumnAndRow($fields['name'], $row)->getFont();
            switch ($font->getSize()) {
                case 14:
                    $this->categories[0] = $data['name'];
                    break;

                case 10:
                    if (!preg_match("~[а-яА-я]+~is", $data['name'])) {
                        $this->brand = $data['name'];
                    } 
                    else {
                        $this->brand = null;
                    }
                    $this->categories[1] = $data['name'];
                    break;
            }

            return null;
        } 

        $data['categories'] = $this->categories;
        $data['brand'] = $this->brand;
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        $pattern = '/(.*)\(ЦЕНА УКАЗАНА ЗА 1 ШТУКУ\!\!\!\)(.*)/si';

        if (preg_match_all($pattern, $data['name'], $matches)) {
            $data['name'] = trim($matches[1][0]);
            $end = trim($matches[2][0]);

            $pattern2 = '/(.*)\((.*шт.*)\)(.*)/si';

            if (preg_match_all($pattern2, $end, $matches2)) {
                $data['name'] .= ' ' . trim($matches2[1][0]);
                if (!empty($matches2[3][0])) {
                    $data['name'] .= ' ' . trim($matches2[3][0]);
                }
            } else {
                $data['name'] .= ' ' . $end;
            }

            $data['name'] .= self::BATTERY_END;
        }

        return $data;
    }
}
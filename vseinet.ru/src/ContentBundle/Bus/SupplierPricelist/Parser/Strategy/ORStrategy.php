<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ORStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код',
                    'name' => 'товар',
                    'description' => 'характеристика',
                    'price' => 'цена, руб.',
                    'availability' => 'фильтр',
                    'min_quantity' => 'кратность',
                ],
            ],
        ];
    }

    protected $categories = [];

    const TYPE = 'БАТАРЕЙКИ';

    const BATTERY_END = ' (цена за 1 шт.)';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price']) {
            $data['categories'] = $this->categories;
            $data['coefficient_price'] = 0.98;
            if (2 < $data['availability']) {
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            }

            if ($this->categories[1] === self::TYPE) {
                $pos = strpos($data['name'], ',');

                if ($pos !== false) {
                    $pattern = '/(.*),(.*)/si';

                    if (preg_match_all($pattern, $data['name'], $matches)) {
                        $data['name'] = trim($matches[1][0]).self::BATTERY_END;
                    }
                } else {
                    $pos = strpos($data['name'], 'шт');
                    if ($pos !== false) {
                        $pattern = '/(.*)\s\-\s(.*?)\sшт/si';

                        if (preg_match_all($pattern, $data['name'], $matches)) {
                            $data['name'] = trim($matches[1][0]).self::BATTERY_END;
                        }
                    } else {
                        $data['name'] .= self::BATTERY_END;
                    }
                }
            }

            return $data;
        }

        if ($data['name']) {
            if (false === strpos($data['name'], '....')) {
                $this->categories[0] = $data['name'];
            }
            $this->categories[1] = ltrim($data['name'], '.');
        }

        return null;
    }
}
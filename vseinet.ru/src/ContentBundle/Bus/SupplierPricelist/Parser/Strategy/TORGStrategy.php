<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TORGStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => [
                        'номенклатура/ характеристика номенклатуры',
                        'характеристика номенклатуры',
                        'номенклатура.брэнд/ номенклатура',
                    ],
                    'category' => [
                        'номенклатура.номенклатурная группа',
                        'н.группа',
                        'номенклатурная группа',
                        'группа товаров',
                    ],
                    'brand' => [
                        'номенклатура.брэнд',
                        'брэнд',
                    ],
                    'description' => [
                        'номенклатура.дополнительное описание',
                        'дополнительное описание',
                    ],
                    'price' => 3,
                    'currency' => 4,
                ],
                'startRow' => 1,
            ],
        ];
    }

    protected $brand;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price']) {
            $data['categories'][] = $data['category'];
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            $data['brand'] = $this->brand;

            return $data;
        }

        $style = $sheet->getStyleByColumnAndRow($fields['name'], $row);
        if ('B4B4B4' == $style->getFill()->getStartColor()->getRGB()) {
            $this->brand = $data['name'];
        }

        return null;
    }
}
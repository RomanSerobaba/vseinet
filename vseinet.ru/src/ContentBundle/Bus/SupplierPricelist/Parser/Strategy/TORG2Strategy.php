<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TORG2Strategy extends AbstractStrategy
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
                    'model' => 'артикул',
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
                    'ccode' => 'номенклатура.код',
                    'price' => 1,
                    'currency' => 2,
                    'availability' => [
                        'остаток',
                    ],
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
            if ($data['availability']) {
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
            }
            $data['article'] = $data['model'];
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
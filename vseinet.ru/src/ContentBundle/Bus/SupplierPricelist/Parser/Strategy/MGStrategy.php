<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MGStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'номенклатура',
                    'code' => 'код номенклатуры',
                    'availability' => ['на складе +', 'на складе +, ++'],
                    'price' => 'цена опт',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['code']) {
            $data['categories'] = $this->categories;
            if ($data['availability']) {
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
            }

            return $data;
        }

        $style = $sheet->getStyleByColumnAndRow($fields['name'], $row);
        
        if ($style->getAlignment()->getIndent()) {
            $this->categories[1] = $data['name'];
        }
        else {
            $this->categories[0] = $data['name'];
        }

        return null;
    }
}
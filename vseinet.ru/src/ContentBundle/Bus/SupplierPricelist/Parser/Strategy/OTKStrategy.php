<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OTKStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'товар',
                    'code' => 'код',
                    'price' => 'цена прайс',
                    'availability' => 1,
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $indent = $sheet->getStyleByColumnAndRow($fields['name'], $row)->getAlignment()->getIndent();

        if (0 == $indent) {
            $this->categories[0] = $data['name'];

            return null;
        } 
        elseif (2 == $indent) {
            $this->categories[1] = $data['name'];

            return null;
        } 

        $data['categories'] = $this->categories;

        if ($data['availability']) {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }
        else {
            $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
        }

        return $data;
    }
}
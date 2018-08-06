<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PREStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код',
                    'name' => 'ценовая группа/ номенклатура/ характеристика номенклатуры',
                    'price' => 'базовая',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $fillColor = $sheet->getStyleByColumnAndRow($fields['name'], $row)->getFill()->getStartColor()->getRGB();

        if ('C3C3C3' == $fillColor) {
            $this->categories[0] = $data['name'];

            return null;
        }

        if ('D2D2D2' == $fillColor) {
            $this->categories[1] = $data['name'];

            return null;
        }

        if ($data['price']) {
            $data['categories'] = $this->categories;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        return null;
    }
}
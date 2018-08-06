<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AUStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код',
                    'name' => ['номенклатура', 'тмц'],
                    'price' => ['цена', 'оптовая цена (руб.)'],
                ],
            ],
        ];
    }

    protected $categories = [];

    protected $prevrow = 0;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price']) {        
            $data['categories'] = $this->categories;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;       
        }

        $style = $sheet->getStyleByColumnAndRow($fields['name'], $row);

        if ($style->getFont()->getBold()) {
            if ($this->prevrow + 1 == $row) {
                $this->categories[] = $data['name'];
            }
            else {
                $this->categories = [$data['name']];
            }
            $this->prevrow = $row;
        }

        return null;
    }
}
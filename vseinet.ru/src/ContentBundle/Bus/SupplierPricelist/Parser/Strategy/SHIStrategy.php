<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SHIStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return array_fill(0, 4, [
            'fields' => [
                'name' => 'номенклатура',
                'availability' => 'центр. склад',
                'price' => 'оптовая',
            ],
        ]);
    }

    protected $category;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $font = $sheet->getStyleByColumnAndRow($fields['name'], $row)->getFont();
        if ($font->getBold()) {
            $this->category = preg_replace('/\s+/', '', str_replace('_', '', $data['name']));

            return null;
        } 
            
        $data['categories'] = [
            $sheet->getTitle(),
            $this->category,
        ];
        
        if ($data['availability']) {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }
        else {
            $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
        }

        return $data;
    }
}
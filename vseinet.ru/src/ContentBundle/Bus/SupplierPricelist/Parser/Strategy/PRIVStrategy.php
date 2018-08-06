<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PRIVStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'наименование',
                    'price' => 'цена, руб.',
                ],
            ],
        ];
    }

    protected $category;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price']) {
            $data['categories'][] = $this->category;
            $data['price_retail_min'] = $data['price'];
            $data['coefficient_price_retail_min'] = 1.3;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        } 

        if ($data['name']) {
            $this->category = $data['name'];
        }
        
        return null;
    }
}
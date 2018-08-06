<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class USLStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category1' => 'категория',
                    'category2' => 'раздел',
                    'name' => 'наименование',
                    'price_retail_min' => 'ррц',
                    'price' => 'или зп',
                ],
            ],
        ];
    }

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $data['categories'] = array_filter([
            $data['category1'],
            $data['category2'],
        ]);
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
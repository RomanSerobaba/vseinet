<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DOKStrategy extends AbstractStrategy
{
    protected $readDataOnly = true; 
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'наименование',
                    'price' => 'цена,руб.', 
                ],
            ],
        ];
    }

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $p = mb_strpos($data['name'], ' ', 0, 'UTF-8');
        $data['categories'][] = mb_substr($data['name'], 0, $p, 'UTF-8');
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
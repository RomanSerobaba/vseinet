<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LUMStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'наименование',
                    'code' => 'артикул',
                    'price' => 'цена',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (false !== strpos($data['name'], 'Бра')) {
            $data['categories'][] = 'Бра';
        }
        else {
            $data['categories'][] = 'Люстры';
        }

        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
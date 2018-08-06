<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UMMAStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код товара',
                    'article' => 'артикул',
                    'brand' => 'производитель',
                    'name' => 'наименование',
                    'model' => 'модель',
                    'description' => 'основные характеристики',
                    'price_retail_min' => 'цена ррц (руб.)',
                    'price' => 'ваша цена ( руб.)',
                ],
            ],
        ];
    }

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['model']) {
            $data['categories'][] = 'Товары';
            $data['name'] = $data['name'] . ' ' . $data['brand'] . ' ' . $data['model'];
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }
        
        return null;
    }
}
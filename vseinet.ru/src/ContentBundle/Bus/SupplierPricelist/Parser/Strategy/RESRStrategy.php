<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RESRStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => ['склад / номенклатура', 'наименование товаров'],
                    'article' => 'артикул',
                    'price' => ['цены отпускные', 'цена отпускная', 'опт', 'цена'],
                    'price_retail_min' => ['цены мрц', 'цена мрц', 'мрц'],
                ],
            ],
        ];
    }

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['price'])) {
            return null;
        }

        $data['categories'][] = 'Товар';
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
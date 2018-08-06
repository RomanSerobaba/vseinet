<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SIGStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'номенклатура',
                    'price' => 'цена',
                ],
            ],
        ];
    }

    protected $category;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (!$data['name']) {
            return null;
        }

        if ('Наименование' == trim($data['name'])) {
            $this->category = null;

            return null;
        }

        if (!$data['price']) {
            $this->category = $data['name'];

            return null;
        }

        $data['categories'][] = $this->category ?: 'Товар';
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
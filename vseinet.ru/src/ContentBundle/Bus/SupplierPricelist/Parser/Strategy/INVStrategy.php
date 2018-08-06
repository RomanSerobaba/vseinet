<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class INVStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'article' => 'артикул',
                    'name' => 5,
                    'price' => 13,
                    'price_retail_min' => 14,
                    'action' => 15,
                ],
            ],
        ];
    }

    protected $category;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['price'])) {
            $this->category = $data['article'] ?: 'Товар';

            return null;
        }

        $data['categories'][] = $this->category;

        if (empty($data['action'])) {
            $data['currency'] = 'USD';
        }

        $data['currency_price_retail_min'] = 'USD';
        
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
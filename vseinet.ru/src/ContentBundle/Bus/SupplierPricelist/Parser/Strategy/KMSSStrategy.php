<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KMSSStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category' => 'номенклатурародитель',
                    'name' => 'номенклатура',
                    'price' => 'цена',
                    'artikul' => 'номенклатуракод',
                ],
            ],
        ];
    }

    protected $category = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['category']) {
            $this->category = $data['category'];

            return null;
        }
        
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        $data['categories'][] = $this->category;
        
        return $data;
    }
}
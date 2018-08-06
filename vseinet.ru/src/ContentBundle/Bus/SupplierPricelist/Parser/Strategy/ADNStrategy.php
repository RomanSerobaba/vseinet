<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ADNStrategy extends AbstractStrategy
{
    protected $readDataOnly = true; 
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category' => 'номенклатура',
                    'name' => 1,
                    'code' => 'артикул (номенклатура)',
                    'brand' => 'производитель (номенклатура)',
                    'price' => 2,
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (!empty($data['code'])) {
            if (false === strpos($data['name'], $data['code'])) {
                $data['name'] = $data['name'].' '.$data['code'];
            }
            $data['model'] = $data['code'];
            $data['categories'] = $this->categories;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        if (!empty($data['category'])) {
            if (false !== strpos($data['category'], '. ')) {
                $this->categories = [
                    $data['category'],
                ];
            }
            elseif (false === strpos($data['category'], '.')) {
                $this->categories[1] = $data['category'];
            }
            else {
                $this->categories[2] = $data['category'];
            }
        }

        return null;
    }
}
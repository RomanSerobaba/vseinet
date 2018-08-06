<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VICLStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'модель',
                    'price' => 'дистибьютор',
                ],
            ],
        ];
    }

    protected $category;

    protected $basename = 'Кондиционер Vico Clima';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['price'])) {            
            $this->category = $data['name'];
            
            return null;
        }

        $data['name'] = $this->basename.' '.$data['name'];
        $data['categories'][] = $this->category;
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
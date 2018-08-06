<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SVKStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'наименование',
                    'options' => 'варианты исполнения',
                    'price' => 1,
                ],
            ],
        ];
    }

    protected $category;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['name'])) {
            return null;
        }
        
        if (empty($data['price'])) {
            $this->category = $data['name'];
            
            return null;
        }
        
        $data['categories'][] = $this->category;
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        $options = explode('-', $data['options']);

        if (1 < count($options)) {
            $variants = [];
            foreach ($options as $option) {
                $variant = $data;
                $variant['name'] = $data['name'].' '.trim($option);
                $variants[] = $variant;
            }

            return $variants;
        } 
        
        $data['name'] = $data['name'].' '.trim($data['options']);
        
        return $data;
    }
}
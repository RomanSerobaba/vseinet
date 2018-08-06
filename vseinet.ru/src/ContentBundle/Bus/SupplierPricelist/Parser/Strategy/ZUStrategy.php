<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZUStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'article' => 'артикул',
                    'name' => 'название',
                    'price' => 'цена, руб.',
                    'min_quantity' => 'min кол-во',
                    'brand' => 'торговая марка',
                    'code' => 'код товара',
                    'availability' => 'остатки',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ('Есть' == $data['availability']) { 
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            $data['categories'][] = $data['brand'];
            if ('Без ТМ' == $data['brand']) {
                $data['brand'] = null;
                $data['name'] .= ', '.$data['article'];
            }
            else {
                $data['name'] = preg_replace('~'.$data['brand'].'~isu', $data['brand'].' '.$data['article'], $data['name']);    
            }

            return $data;
        }

        return null;
    }
}
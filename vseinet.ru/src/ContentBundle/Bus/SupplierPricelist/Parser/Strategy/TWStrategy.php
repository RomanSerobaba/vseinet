<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TWStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'Код',
                    'name' => 'Номенклатура',
                    'brand' => 'Бренд',
                    'category1' => 'Группа №1',
                    'category2' => 'Группа №2',
                    'category3' => 'Группа №3',
                    'category4' => 'Группа №4',
                    'price' => 'Цена',
                    'availability' => 'Наличие',
                    'description' => 'Описание',
                    'bar_codes' => 'Штрихкод EAN13',
                ],
            ],
        ];
    }

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['name'])) {
            return null;
        }

        $data['categories'] = [
            $data['category1'],
            $data['category2'],
            $data['category3'],
            $data['category4'],
        ];

        switch ($data['availability']) {
            case 'в наличии':
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
                break;

            case 'под заказ':
                $data['availability'] = ProductAvailabilityCode::ON_DEMAND;
                break;

            default:
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
        }

        if (!empty($data['bar_codes'])) {
            $data['bar_codes'] = array_map('trim', explode(',', $data['bar_codes']));
        }

        $data['coefficient_price'] = 0.93;

        return $data;        
    }
}
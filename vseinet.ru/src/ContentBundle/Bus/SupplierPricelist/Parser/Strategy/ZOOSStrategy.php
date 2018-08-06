<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZOOSStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'article' => 'артикул',
                    'category' => 'группа',
                    'name' => 'наименование',
                    'min_quantity' => 'мин.партия',
                    'price' => 'цена',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (in_array($data['category'], ['264  АКАНА', '265  ОРИДЖЕН', '170  EVANGERS', '270  BLITZ'])) {
            $data['coefficient_price'] = 0.97;
        }
        elseif (in_array($data['category'], ['565  БАЙЕР', '481  ЛАКОМСТВА/КОСТИ', '614  М.Бруно', '312  BARKING HEADS'])) {
            $data['coefficient_price'] = 0.92;
        }
        elseif (in_array($data['category'], ['124  ХИЛЛС'])) {
            // absolute
        }
        else {
            $data['coefficient_price'] = 0.91;
        }

        $data['categories'][] = $data['category'];
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
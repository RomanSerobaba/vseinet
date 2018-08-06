<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BEStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;

    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'название',
                    'article' => 'артикул',
                    'brand' => 'торговая марка',
                    'code' => 'код товара',
                    'price' => 'оптовая цена',
                    'bar_code' => 'штрих-код',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price']) {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            $data['categories'] = $this->categories;
            $data['coefficient_price'] = 0.85;
            $data['bar_codes'] = array_map('trim', explode(',', $data['bar_code']));

            return $data;
        }

        $name = trim($data['name']);
        if ('-' == substr($name, 0, 1)) {
            $this->categories[1] = substr($name, 1);
        }
        else {
            $this->categories[0] = $name;
        }

        return null;
    }
}
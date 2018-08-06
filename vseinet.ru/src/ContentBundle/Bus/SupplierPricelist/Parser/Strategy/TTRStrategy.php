<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TTRStrategy extends AbstractStrategy
{
    protected $readDateOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'price' => 'ваша цена со скидкой, $',
                    'price_retail_min' => -1,
                    'name' => -8,
                ],
            ],
            1 => [
                'fields' => [
                    'price' => 'ваша цена со скидкой, $',
                    'price_retail_min' => -1,
                    'name' => -8,
                ],
            ],
        ];
    }

    protected $category;

    protected $brand;

    protected $basename = 'Кондиционер';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ('' != $data['price'] && '-' != $data['price'] && '' != $data['name']) {
            $data['currency'] = 'USD';
            $data['brand'] = $this->brand ?: 'Sakata';
            $data['name'] = $this->basename.' '.$data['name'];
            $data['categories'][] = $this->category;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        if ('' != $data['name']) {
            $this->category = $data['name'];
            if (preg_match('/\*(.*)/', $this->category, $matches)) {
                $this->brand = isset($matches[1]) ? $matches[1] : null;
            }
        }

        return null;
    }
}
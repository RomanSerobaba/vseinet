<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BLStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код',
                    'article' => 'артикул',
                    'brand' => 'бренд',
                    'name' => 'наименование',
                    'description' => 'описание',
                    'price' => 'цена',
                    'price_retail_min' => 'риц',
                    'availability' => 'наличие',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $style = $sheet->getStyleByColumnAndRow($fields['code'], $row);

        if (12 == $style->getFont()->getSize()) {
            $this->categories[0] = $data['code'];

            return null;
        }
        if (10 == $style->getFont()->getSize()) {
            $this->categories[1] = $data['code'];

            return null;
        }
        
        if (empty($data['name'])) {
            return null;
        }

        $data['categories'] = $this->categories;
        
        if ($data['price_retail_min'] && 'РРЦ' != $this->pricelistName) {
            $data['price_retail_min'] = 0;
        }
        
        if ('приход' == $data['availability']) {
            $data['availability'] = ProductAvailabilityCode::IN_TRANSIT;
        }
        elseif ($data['availability']) {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }
        else {
            $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
        }
        
        return $data;
    }
}
<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RLFStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код',
                    'name' => 'наименование',
                    'url_image' => 'фото',
                    'article' => 'артикул',
                    'brand' => 'произ-ль',
                    'price' => 'предложение (пнз)',
                    'min_quantity' => 'мин уп',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['code']) {
            $data['categories'] = $this->categories;
            if (!empty($data['url_image'])) {
                $data['url_images'][] = 'https://relefopt.ru/'.$data['url_image'];
            }
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        $style = $sheet->getStyleByColumnAndRow($fields['name'], $row);        
        if (14 == $style->getFont()->getSize()) {
            $this->categories[0] = $data['name'];
        } 
        else {
            $this->categories[1] = $data['name'];
        }
        
        return null;
    }
}
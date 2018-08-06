<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CAStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'наименование товара',
                    'description' => 'краткое описание',
                    'price' => 'цена',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['name'])) {
            return null;
        }

        $font = $sheet->getStyleByColumnAndRow($fields['name'], $row)->getFont();
        if ($font->getBold()) {
            $this->categories[0] = $data['name'];

            return null;
        }

        if (false !== ($p = strpos($data['description'], 'На заказ!'))) {
            $data['description'] = trim(substr($data['description'], $p + 10));
            $data['availability'] = ProductAvailabilityCode::IN_TRANSIT;
        }
        else {
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
        }

        $data['categories'] = $this->categories;

        return $data;
    }
}
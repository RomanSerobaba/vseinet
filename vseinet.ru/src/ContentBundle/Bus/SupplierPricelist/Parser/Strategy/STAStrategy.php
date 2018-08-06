<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class STAStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'группа/товар',
                    'price' => 'опт',
                    'availability' => 'наличие центр.склад',
                    'currency' => 'оптвал',
                    'price_for_retail' => 'ррц, руб.',
                ],
            ],
        ];
    }

    protected $priceRetailBrands = [
        'Pandora',
        'StarLine',
        'Tiger Shark',
        'Alpine',
        'ARIA',
        'SWAT',
    ];

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data['price'])) {
            $font = $sheet->getStyleByColumnAndRow($fields['name'], $row)->getFont();
            if (10 == $font->getSize()) {
                $this->categories[1] = $data['name'];
            }
            else {
                $this->categories[0] = $data['name'];
                $this->categories[1] = 'Товар';
            }

            return null;
        }
        
        if (false !== strpos($data['name'], 'Alpine') || empty($data['availability'])) {
            return null;
        }

        $data['categories'] = $this->categories;

        foreach (array_merge($this->categories, [$data['name']]) as $value) {
            foreach ($this->priceRetailBrands as $brand) {
                if (false !== strpos($value, $brand)) {
                    $data['price_retail_min'] = $data['price_for_retail'];
                    break 2;
                }
            }
        }

        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
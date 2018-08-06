<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NIStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'image' => 'изображение',
                    'name' => 'модель',
                    'price' => 'опт, $',
                    'price_retail_min' => 'розница, $'
                ],
            ],
            2 => [
                'fields' => [
                    'image' => 'изображение',
                    'name' => 'модель',
                    'price' => 'опт, $',
                    'price_retail_min' => 'розница, $'
                ],
            ],
            5 => [
                'fields' => [
                    'name' => 'наименование',
                    'not_avail' => 'не обрабатываемые',
                    'price' => 'дил.',
                ],
            ],
            6 => [
                'fields' => [
                    'name' => 'наименование',
                    'not_avail' => 'не обрабатываемые',
                    'price' => 'дил.',
                ],
            ],
            7 => [
                'fields' => [
                    'name' => 'наименование',
                    'not_avail' => 'не обрабатываемые',
                    'price' => 'дил.',
                ],
            ],
            8 => [
                'fields' => [
                    'name' => 'наименование',
                    'not_avail' => 'не обрабатываемые',
                    'price' => 'дил.',
                ],
            ],
            9 => [
                'fields' => [
                    'name' => 'модель',
                    'not_avail' => 'не обрабатываемые',
                    'price' => 'цена опт usd',
                ],
            ],
        ];
    }

    protected $category;

    protected $basename = 'Кондиционер';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (in_array($sheet->getTitle(), ['General Cl', 'Полупром General Cl'])) {
            if ($data['price'] && $data['name']) {
                $data['categories'][] = $this->category;
                if (false === strpos($data['name'], $this->basename)) {
                    $data['name'] = $this->basename.' '.$data['name'];
                }
                $data['currency'] = 'USD';
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;

                return $data;
            }
            $this->category = $data['image'];
        }
        elseif (in_array($sheet->getTitle(), ['LG', 'Hitachi', 'Samsung', 'Toshiba', 'Panasonic'])) {
            if ('1' != $data['not_avail'] && $data['price']) {
                $data['brand'] = $sheet->getTitle();
                $data['categories'][] = $this->category;
                if (false === strpos($data['name'], $this->basename)) {
                    $data['name'] = $this->basename.' '.$data['name'];
                }
                $data['currency'] = 'USD';
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;

                return $data;
            }
            $this->category = $data['name'];
        }

        return null;
    }
}
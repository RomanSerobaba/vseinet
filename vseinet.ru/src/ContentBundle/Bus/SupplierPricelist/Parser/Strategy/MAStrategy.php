<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MAStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'category' => 'модель',
                    'name' => 1,
                    'desc1' => 'ширина',
                    'desc2' => 'цвет',
                    'desc3' => 'производительность м3 / час',
                    'desc4' => 'управление',
                    'desc5' => 'освещение',
                    'desc6' => 'таймер (мин)',
                    'price_usd' => 'внутренний курс',
                    'price' => 1,
                    'price_retail_min' => 'мрц',
                ],
            ],
        ];
    }

    protected $category;

    protected $basename;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['category']) {
            $this->category = $data['category'];

            return null;
        }

        if ($data['name']) {
            if ($data['desc2']) {
                $this->basename = $data['desc2'];
            }
            $data['brand'] = 'Akpo';
            $data['name'] = 'Вытяжка '.$data['brand'].' '.$data['name'].' '.$data['desc1'].' '. $this->basename;
            $data['description'] = 'Ширина: '.$data['desc1'].'Цвет: '.$data['desc2'].'Производительность м3 / час: '.$data['desc3'].'. Управление: '.$data['desc4'].'. Освещение: '.$data['desc5'].'. Таймер (мин): '.$data['desc6'].'.';
            $data['categories'][] = $this->category;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        return null;
    }
}
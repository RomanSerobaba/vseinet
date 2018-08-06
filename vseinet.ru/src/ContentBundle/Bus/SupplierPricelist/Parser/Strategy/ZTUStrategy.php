<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZTUStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'article' => 'номенклатура.артикул',
                    'name' => [
                        'ценовая группа/ номенклатура',
                        'ценовая группа/ номенклатура/ характеристика номенклатуры',
                        'номенклатура/ характеристика номенклатуры',
                    ],
                    'price' => 'опт базовый',
                    'price_retail_min' => 'ррц',
                ],
            ],
        ];
    }

    protected $categories = [];

    protected $name;

    protected $url;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        preg_match('/^(\s*)\S+/', $data['name'], $m);
        $data['name'] = trim($data['name']);
        if ($data['article']) {
            $data['url'] = $this->url;
            if ('----//----' == $data['name']) {
                $data['name'] = $this->name;
            } 
            elseif (12 == strlen($m[1])) {
                $data['name'] = $this->name.' '.$data['name'];
            }
            else {
                $cell = $sheet->getCellByColumnAndRow($fields['name'], $row);
                if ($cell->hasHyperlink()) {
                    $data['url'] = $cell->getHyperlink()->getUrl();
                }
            }
            if ($data['url']) {
                $p = strpos(trim($data['url']), "\0");
                if (false !== $p) {
                    $data['url'] = substr($data['url'], 0, $p);
                }
            }
            if (4 == strlen($m[1])) {
                $this->categories[1] = 'Товар';
            }
            $data['categories'] = $this->categories;
            if ($data['price_retail_min'] && !preg_match('~(Trek\sPlanet|Relax|Fishman|Wanderlust)~isu', $data['name'])) {
                $data['coefficient_price_retail_min'] = 0.95;
            }
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        if (!empty($m) && 1 < count($m)) {
            switch (strlen($m[1])) {
                case 0:
                    $this->categories[0] = $data['name'];
                    break;

                case 4:
                    $this->categories[1] = $data['name'];
                    break;

                case 8:
                    $this->name = $data['name'];
                    $cell = $sheet->getCellByColumnAndRow($fields['name'], $row);
                    $this->url = $cell->hasHyperlink() ? $cell->getHyperlink()->getUrl() : null;
                    break;
            }
        }

        return null;
    }
}
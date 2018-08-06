<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ELStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'код',
                    'name' => 'парфюмерия',
                    'price' => 'цена*, р',
                ],
            ],
            1 => [
                'fields' => [
                    'code' => 'код',
                    'name' => 'косметика',
                    'price' => 'цена*, р',
                ],
            ],
        ];
    }

    protected $category;

    protected $brand;

    protected $name;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (in_array($sheet->getTitle(), ['Parfume', 'Парфюмерия', 'Парфюмерия оптом'])) { 
            if ($data['code']) {
                $data['categories'] = ['Парфюмерия', $this->category];
                $data['brand'] = $this->brand;
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;

                return $data;
            }

            $this->category = $this->brand = $data['name'];
        }
        elseif (in_array($sheet->getTitle(), ['Cosmetics', 'Косметика', 'Косметика оптом'])) {
            if ($data['code']) {
                $data['categories'] = ['Косметика', $this->category];
                $data['brand'] = $this->brand;
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;

                return $data;
            }

            if ($this->name && false === strstr($this->name, $this->brand)) {
                $this->brand = $this->name;
            }

            $this->name = $data['name'];
            $this->category = str_replace('"', '', preg_replace("~^({$this->brand}|C.\s*DIOR|YSL|SLLUX|TSUBAKI|CD|ARMANI)\s+~is", '', $data['name']));
        }

        return null;
    }
}
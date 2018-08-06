<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ORHStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'артикул',
                    'name' => 'наименование товара',
                    'price' => 'цена (руб)',
                    'availability' => 1,
                ],
            ],
        ];
    }

    protected $cats96 = [
        '08. ДВЕРИ',
        '19. ШВЕЙНЫЕ ИЗДЕЛИЯ "ДОБРОШВЕЙКИН"',
        '05. ЗАМКИ, ПЕТЛИ, СКОБЯНЫЕ ИЗДЕЛИЯ',
        '06. ИНСТРУМЕНТ РУЧНОЙ',
        '07. ЛАКОКРАСОЧНАЯ ПРОДУКЦИЯ',
        '02. ОТДЕЛОЧНЫЕ МАТЕРИАЛЫ',
        '11. ПОГОНАЖНЫЕ ИЗДЕЛИЯ ИЗ ДЕРЕВА',
        '10. САНКИ',
        '03. САНТЕХНИКА',
        '04. САДОВО-ОГОРОДНЫЙ ИНВЕНТАРЬ',
        '09. СПЕЦОДЕЖДА, ПЕРЧАТКИ, РУКАВИЦЫ',
        '01. СТРОИТЕЛЬНЫЕ МАТЕРИАЛЫ',
        '12. ВЕШАЛКИ',
    ];

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['name'] && $data['price'] && $data['availability']) {
            $data['coefficient_price'] = in_array($this->categories[0], $this->cats96) ? 0.96 : 0.94;
            $data['categories'] = $this->categories;
            if ('Витрина' == $data['availability']) {
                $data['availability'] = ProductAvailabilityCode::OUT_OF_STOCK;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            }

            return $data;
        }
        
        $font = $sheet->getStyleByColumnAndRow($fields['code'], $row)->getFont();
        if ('FF0000' == $font->getColor()->getRGB()) {
            if (11 == $font->getSize()) {
                $this->categories[0] = preg_replace('~\s*\(\d+\)~isu', '', $data['code']);
            }
            elseif ($font->getItalic()) {
                $this->categories[1] = preg_replace('~\s*\(\d+\)~isu', '', $data['code']);
                unset($this->categories[2]);
            }
            else {
                $this->categories[2] = preg_replace('~\s*\(\d+\)~isu', '', $data['code']);
            }
        }

        return null;
    }
}
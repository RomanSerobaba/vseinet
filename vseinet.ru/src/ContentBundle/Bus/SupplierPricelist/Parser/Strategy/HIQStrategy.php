<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HIQStrategy extends AbstractStrategy
{
    protected $readDataOnly = true; 
    
    public function getSheets()
    {
        return [
            1 => [
                'fields' => [
                    'category' => '№',
                    'name' => 'наименование',
                    'description' => 'краткие характеристики',
                    'description2' => 4,
                    'price_retail_min' => 'розничная цена (рекоменд.)',
                    'price' => 'партнер'
                ],
            ],
        ];
    }

    protected $category;

    protected $description;

    protected $productName;

    protected $tmpPrice;

    protected $tmpRetailPrice;

    protected $tmpName;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price'] && $data['category']) {
            $this->tmpPrice = $data['price'];
            $this->tmpRetailPrice = $data['price_retail_min'];
        } 
        else {
            $this->tmpPrice = '';
            $this->tmpRetailPrice = '';
        }

        if ('11. Гибридные видеорегистраторы' == $this->category || '12. AHD-видеорегистраторы.' == $this->category || '13. Сетевые видеорегистраторы' == $this->category) {
            if ($data['name'] && !$data['price']) {
                $this->tmpName = ' '.$data['name'];
            }
        }

        if (($data['price'] && $data['category']) || $this->tmpPrice != '' || ($data['price'] && $this->tmpName != '')) {
            switch($this->category) {
                case '1.Камеры внутренние':
                    $this->productName = 'Камера внутренняя ';
                    break;
                case '2.Камеры внутренние купольные с ИК подсветкой':
                    $this->productName = 'Камера внутренняя купольная с ИК подсветкой ';
                    break;
                case '3.Камеры уличные с ИК подсветкой':
                    $this->productName = 'Камера уличная с ИК подсветкой ';
                    break;
                case '4. Поворотные видеокамеры':
                    $this->productName = 'Поворотная видеокамера ';
                    break;
                case '5. Внутренние AHD Камеры (От -10 до +40°C)':
                    $this->productName = 'Внутренняя AHD камера ';
                    break;
                case 'Уличные AHD камеры (От -40 до +50°C)':
                    $this->productName = 'Уличная AHD камера ';
                    break;
                case 'IP-видеокамеры':
                    $this->productName = 'IP-видеокамера ';
                    break;
                case '7. IP-камеры уличные':
                    $this->productName = 'IP-камера уличная ';
                    break;
                case '8. Муляжи камер видеонаблюдения':
                    $this->productName = 'Муляж камер видеонаблюдения ';
                    break;
                case '9. ИК-подсветка/прожекторы':
                    $this->productName = 'Прожектор ';
                    break;
                case '10. Плата для видеозахвата':
                    $this->productName = 'Плата для видеозахвата ';
                    break;
                case '11. Гибридные видеорегистраторы':
                    $this->productName = 'Гибридный видеорегистратор ';
                    break;
                case '12. AHD-видеорегистраторы.':
                    $this->productName = 'AHD-видеорегистратор ';
                    break;
                case '13. Сетевые видеорегистраторы':
                    $this->productName = 'Сетевой видеорегистратор ';
                    break;
                case '14. Видеодомофоны/СКУД':
                    $this->productName = 'Монитор для видеодомофонов ';
                    break;
                case '16. Микрофоны':
                    $this->productName = 'Микрофон ';
                    break;
                case '17. Объективы с фиксированным фокусным расстоянием, резьба М12':
                    $this->productName = 'Объектив с фиксированным фокусным расстоянием ';
                    break;
                case '19. Вариофокальные объективы, резьба М12':
                    $this->productName = 'Варифокальный объектив ';
                    break;
                default:
                    $this->productName = '';
                    break;
            }
            $data['categories'] = [$this->category];
            $data['name'] = $this->productName.$this->tmpName.$data['name'];
            if(!empty($data['description2'])) {
                $data['description'] .= ' '.$data['description2'];
            }
            $data['description'] = htmlspecialchars($data['description'], ENT_QUOTES);
            if($this->tmpPrice) {
                $data['price'] = $this->tmpPrice;
            }
            if($this->tmpRetailPrice) {
                $data['price_retail_min'] = $this->tmpRetailPrice;
            }
            if($this->tmpName != '') {
                $this->tmpName = '';
            }

            $data['coefficient_price_retail_min'] = 0.9;
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        if ($data['category'] && empty($data['name'])) {
            $this->category = $data['category'];
        }

        return null;
    }
}
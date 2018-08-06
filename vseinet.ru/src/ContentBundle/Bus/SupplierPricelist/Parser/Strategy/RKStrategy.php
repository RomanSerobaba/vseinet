<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RKStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            '1.Пушки эл' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'performance' => 'производи-тельность, м3/ч',
                    'description' => 'особенности',
                    'price' => 'опт, руб',
                ],
            ],
            '2.Пушки газ' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'performance' => 'производительность, м3/ч',
                    'price' => 'опт, руб',
                ],
            ],                
            'Пушки диз' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'performance' => 'производи-тельность, м3/ч',
                    'price' => 'опт, usd',
                ],
            ],
            '3.ИК обогр.эл' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность, квт',
                    'description' => 'особенности',
                    'price' => 'опт, руб',
                ],
            ],
            '4.ИК обогр.газ' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'area' => 'площадь обогрева, м2',
                    'igniter' => 'розжиг',
                    'description' => 'особенности',
                    'price' => 'опт, usd',
                ],
            ],
            '4.1 ИК обогр.газ' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'area' => 'площадь обогрева, м2',
                    'igniter' => 'розжиг',
                    'description' => 'особенности',
                    'price' => 'опт, руб',
                ],
            ],
            '5.Завесы компактные' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'performance' => 'производи-тельность, м3/ч',
                    'height' => 'высота установки, м',
                    'price' => 'опт, руб',
                ],
            ],
            '6. Завесы эл. пром' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'performance' => 'производи-тельность, м3/ч',
                    'height' => 'высота установки, м',
                    'price' => 'опт, руб',
                ],
            ],
            '7.Завесы и тепловент. вод' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность, квт*',
                    'performance' => 'производитель-ность, м3/ч',
                    'height' => 'высота установки, м',
                    'price' => 'опт, руб',
                ],
            ],
            '8.Завесы STELLA' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'тепловая мощность, квт',
                    'performance' => 'производительность, м3/ч',
                    'price' => 'опт, руб',
                ],
            ],
            'Масл.радиаторы' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность',
                    'area' => 'площадь обогрева, м2',
                    'description' => 'основные преимущества',
                    'price' => 'цена опт, usd', 
                ],
            ],
            'тепловентиляторы' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность',
                    'area' => 'площадь обогрева, м2',
                    'description' => 'основные преимущества',
                    'price' => 'цена опт, usd', 
                ],
            ],
            'Конвекторы и сушилки для рук' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность, вт',
                    'area' => 'площадь обогрева, м2',
                    'description' => 'основные преимущества',
                    'price' => 'цена опт, руб',
                ],
            ],
            'конвекторы баксы' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность, вт',
                    'area' => 'площадь обогрева, м2',
                    'description' => 'основные преимущества',
                    'price' => 'цена опт, usd',
                ],
            ],
            'сушилки для рук' => [
                'fields' => [
                    'model' => 'модель',
                    'power' => 'мощность, вт',
                    'description' => 'основные преимущества',
                    'price' => 'цена опт, руб',
                ],
            ],
        ];
    }

    protected $category;

    protected $power;

    protected $performance;

    protected $aria;

    protected $igniter;

    protected $height;

    protected $description;

    protected function get($data, $key) 
    {
        if (!empty($data[$key])) {
            if ('-' == trim($data[$key])) {
                $this->$key = null;
            }
            else {
                $this->$key = trim($data[$key]); 
            }
        }

        return $this->$key;
    }

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data)) {
            return null;
        }
        
        if ($data['price']) {
            if (false !== strpos($fields['price']['title'], 'usd')) {
                $data['currency'] = 'USD';
            }
            $data['name'] = $this->category.' Ballu '.$data['model'];
            if ($power = $this->get($data, 'power')) {
                $data['name'] .= '; '.str_replace(' ', '', $power).' кВт';
            }
            if ($performance = $this->get($data, 'performance')) {
                $data['name'] .= '; '.$performance.' м3/ч';
            }
            if ($area = $this->get($data, 'area')) {
                $data['name'] .= '; '.$area.' м2';
            }
            if ($igniter = $this->get($data, 'igniter')) {
                $data['name'] .= '; розжиг - '.mb_strtolower($igniter);
            }
            if ($height = $this->get($data, 'height')) {
                $data['name'] .= '; высота установки '.$height.' м';
            }
            $data['categories'] = [
                'Ballu',
                $this->category,
            ];
            $data['brand'] = 'Ballu';
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            if ($description = $this->get($data, 'description')) {
                $data['description'] = $description;
            }

            return $data;
        }

        $this->category = $data['model'];
        $this->power = null;
        $this->performance = null;        
        $this->area = null;        
        $this->igniter = null;    
        $this->height = null;    
        $this->description = null;

        return null;
    }
}
<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SPZ8Strategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'name' => 'модель (наружный / внутр. блок)',
                    'price' => 'цена дилер., usd',
                    'action_price_usd' => 'акция, usd',
                    'action_price_rub' => 'акция, руб.',
                ],
            ],
            1 => [
                'fields' => [
                    'name' => 'модель',
                    'price' => 'цена дилерская, usd',
                    'action_price_usd' => 'акция, usd',
                    'action_price_rub' => 'акция, руб.',
                ],
            ],
            2 => [
                'fields' => [
                    'name' => 'модель',
                    'price' => 'дилер пред , у.е.',
                    'price_retail_min' => 'мрц, руб.',
                    'action_price_usd' => 'акция, usd',
                    'action_price_rub' => 'акция, руб.',
                ],
            ],
            3 => [
                'fields' => [
                    'name' => 'модель',
                    'price' => 'd min, usd',
                    'price_retail_min' => 'розница, usd',
                    'action_price_usd' => 'акция, usd',
                    'action_price_rub' => 'акция, руб.',
                ],
            ],
            4 => [
                'fields' => [
                    'name' => 'модель',
                    'price' => 'розн. цена, руб.',
                    'discount' => 'скидка, %',
                ],
            ],
        ];
    }

    protected $discount = 100;

    protected $basename = 'Кондиционер';

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $lotitle = mb_strtolower($sheet->getTitle(), 'UTF-8');
        if ('тепломаш завесы электр' == $lotitle) {
            $this->basename = 'Тепловая завеса';
            if (!empty($data['discount'])) {
                $this->discount = 100 - $data['discount'];
            }
        }
        elseif (false !== strpos($data['name'], 'Кондиционер')) {
            $data['name'] = trim(str_replace('Кондиционер' , '', $data['name']));
        }
        if ('' != $data['price'] && '-' != $data['price']) {
            $data['categories'][] = $sheet->getTitle();
            $data['name'] = $this->basename.' '.$data['name'];
            if ('тепломаш завесы электр' == $lotitle) {
                $data['coefficient_price'] = $this->discount / 100;
            }
            else {
                if ('' != $data['action_price_rub']) {
                    $data['price'] = $data['action_price_rub'];
                }
                else {
                    $data['currency'] = 'USD';
                    if ('' != $data['action_price_usd']) {
                        $data['price'] = $data['action_price_usd'];
                    }
                }
                if ('toshiba' == $lotitle) {
                    $data['currency_price_retail_min'] = 'USD';
                }
            }
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        }

        return null;
    }
}
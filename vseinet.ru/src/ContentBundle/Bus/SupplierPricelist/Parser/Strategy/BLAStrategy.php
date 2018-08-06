<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BLAStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'article' => 'артикул',
                    'brand' => 'бренд',
                    'name' => 'наименование',
                    'description' => 'описание',
                    'price' => 'опт. цена',
                    'price_retail_min' => 'миц',
                ],
            ],
        ];
    }

    protected $categories = [];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if ($data['price']) {
            if ('-' == substr($data['price'], 0, 1)) { // hack
                return null;
            }

            $currency = $sheet->getStyleByColumnAndRow($fields['price'], $row)->getNumberFormat()->getFormatCode();
            if (preg_match('~евро~isu', $currency)) {
                $data['currency'] = 'EUR';
            }
            elseif (preg_match('~доллар~isu', $currency)) {
                $data['currency'] = 'USD';
            }
            if ($data['price_retail_min']) {
                $currency = $sheet->getStyleByColumnAndRow($fields['price_retail_min'], $row)->getNumberFormat()->getFormatCode();
                
                if (preg_match('~евро~isu', $currency)) {
                    $data['currency_price_retail_min'] = 'EUR';
                }
                elseif (preg_match('~доллар~isu', $currency)) {
                    $data['currency_price_retail_min'] = 'USD';
                } 
                
                $data['coefficient_price_retail_min'] = 0.95;
            }
            if ('SMEG' == $data['brand']) {
                $data['price_retail_min'] = 0;                
            }
            
            $data['categories'] = $this->categories;
            $data['availability'] = ProductAvailabilityCode::ON_DEMAND;

            return $data;
        }
            
        $style = $sheet->getStyleByColumnAndRow($fields['name'], $row);

        if ('CCFFFF' == $style->getFill()->getStartColor()->getRGB()) {
            $this->categories[0] = $data['article'];
        } 
        elseif ('CCFFCC' == $style->getFill()->getStartColor()->getRGB()) {
            $this->categories[1] = $data['article'];
        }

        return null;
    }
}
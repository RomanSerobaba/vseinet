<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XXXStrategy extends AbstractStrategy
{
    public function getSheets()
    {
        return array_fill(0, 9, [
            'fields' => [
                'model' => 'модель',
                'description' => 'описание',
                'price' => 3,
            ]
        ]);
    }

    protected $category;

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        if (empty($data)) {
            return null;
        }
        
        if ($data['description']) {
            $data['categories'][] = $this->category ?: $sheet->getTitle();
            preg_match('~^(.*?)(\n|\r|\s{2,}|\.)~iu', $data['description'], $matches);
            if(!empty($matches[1]) && 150 > strlen($matches[1])) {
                $data['name'] = trim($matches[1].' '.$data['model']);
            } 
            else {
                $data['name'] = $data['model'];
            }
            $data['availability'] = ProductAvailabilityCode::AVAILABLE;

            return $data;
        } 
        elseif ('000000' != $sheet->getStyleByColumnAndRow($fields['model'], $row)->getFill()->getStartColor()->getRGB()) {
            $this->category = $data['model'];
        }

        return null;
    }
}
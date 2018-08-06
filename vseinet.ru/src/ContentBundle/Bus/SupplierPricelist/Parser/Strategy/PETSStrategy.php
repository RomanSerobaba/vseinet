<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser\Strategy;

use ContentBundle\Bus\SupplierPricelist\Parser\AbstractStrategy;
use AppBundle\Enum\ProductAvailabilityCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PETSStrategy extends AbstractStrategy
{
    protected $readDataOnly = true;
    
    public function getSheets()
    {
        return [
            0 => [
                'fields' => [
                    'code' => 'артикул',
                    'brand' => 'брэнд',
                    'category' => 'категория',
                    'model' => 'название',
                    'weight' => 'масса объем длина',
                    'price' => 'скидка',
                ],
            ],
        ];
    }

    const PATTERNS = [
        '~консервированный~isu', 
        '~корм~isu', 
        '~кости~isu', 
        '~лакомства~isu', 
        '~каши~isu', 
        '~сухой~isu', 
        '~наполнители~isu',   
    ];

    public function processData(array $data, Worksheet $sheet, int $row, array $fields)
    {
        $data['name'] = $data['brand'].' '.$data['model'];
        $data['weight'] = floatval(str_replace(',', '.', $data['weight']));

        if ($data['weight']) {
            foreach (self::PATTERNS as $pattern) {
                if (preg_match($pattern, $data['category']) && !preg_match('~\s+[\d,]+\s*л(\s|$)~ius', $data['name'])) {
                    $data['name'] .= ', '.(round($data['weight']) < 1 ? ($data['weight'] * 1000).' г' : $data['weight'].' кг'); 
                    break;
                }
            }
        }

        $data['categories'][] = $data['category'] ?: 'Товар';
        $data['availability'] = ProductAvailabilityCode::AVAILABLE;

        return $data;
    }
}
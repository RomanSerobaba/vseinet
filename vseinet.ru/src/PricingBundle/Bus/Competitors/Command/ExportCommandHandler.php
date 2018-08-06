<?php 

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\MessageHandler;
use PricingBundle\Bus\Competitors\Query\GetIndexQuery;
use PricingBundle\Component\CompetitorsComponent;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Component\OrderComponent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportCommandHandler extends MessageHandler
{
    public function handle(ExportCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = new GetIndexQuery($command->toArray());

        $component = new CompetitorsComponent($em);
        $list = $component->getIndexList($query);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Vseinet.ru')
            ->setTitle('Сверка с конкурентами')
            ->setSubject('Office 2007 XLSX Document');

        $spreadsheet->setActiveSheetIndex(0);

        $titles = [];
        $titles[] = ['name' => '№', 'code' => 'id',];
        $titles[] = ['name' => 'Код', 'code' => 'code',];
        $titles[] = ['name' => 'Товар', 'code' => 'product',];
        $titles[] = ['name' => 'Цена закупки', 'code' => 'contractor_price',];
        $titles[] = ['name' => 'Наценка', 'code' => 'delta',];
        $titles[] = ['name' => 'Цена', 'code' => 'price',];

        $columnsCount = count($titles) + count($list['competitors']);

        for ($i = 65; $i < 65 + $columnsCount; $i++)
            $spreadsheet->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);

        $index = 0;
        foreach ($titles as $title) {
            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index++) . '1', $title['name']);
        }
        foreach ($list['competitors'] as $name) {
            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index++) . '1', $name);
        }

        for ($i = 65; $i < 65 + count($titles); $i++) {
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFill()->setFillType(Fill::FILL_SOLID);
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFill()->getStartColor()->setRGB('444444');
        }

        for ($i = 65 + count($titles); $i < 65 + $columnsCount; $i++) {
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFill()->setFillType(Fill::FILL_SOLID);
            $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFill()->getStartColor()->setRGB('999999');
        }

        $data = [];
        $this->_processData($list['data'][0], $data);

        $itemsIndex = 1;
        $rowNumber = 2;
        foreach ($data as $info) {
            if (!is_array($info)) {
                $spreadsheet->getActiveSheet()->mergeCells(chr(65).$rowNumber.':'.chr(65 + $columnsCount - 1).$rowNumber);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowNumber, $list['categories'][$info]);

                $spreadsheet->getActiveSheet()->getStyle(chr(65) . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID);
                $spreadsheet->getActiveSheet()->getStyle(chr(65) . $rowNumber)->getFill()->getStartColor()->setRGB('D3D3D3');
            } else {
                $index = 0;
                foreach ($titles as $key => $title) {
                    switch ($title['code']) {
                        case 'id':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, $itemsIndex++);
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;

                        case 'code':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, html_entity_decode($info['code'] ?? '', ENT_QUOTES));
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                            break;

                        case 'product':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, html_entity_decode($info['product'], ENT_QUOTES));
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                            break;

                        case 'contractor_price':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, $info['contractor_price']/100);
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;

                        case 'delta':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, $info['delta'].'%');
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;

                        case 'price':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, $info['price']/100);
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;
                    }
                    $index++;
                }

                foreach ($list['competitors'] as $competitorId => $competitorName) {
                    $value = !empty($info['competitors'][$competitorId]) ? $info['competitors'][$competitorId]['price'] / 100 : '';
                    $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $index) . $rowNumber, $value);
                    $spreadsheet->getActiveSheet()->getStyle(chr(65 + $index) . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $index++;
                }
            }

            $rowNumber++;
        }

        $spreadsheet->getActiveSheet()->calculateColumnWidths();

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save($command->fileName);

        chmod($command->fileName, 0777);
    }

    private function _processData($data, &$list)
    {
        foreach ($data as $key => $value) {
            if ($key === 'items') {
                foreach ($value as $item) {
                    $list[] = $item;
                }
            } else {
                $list[] = $key;
                $this->_processData($data[$key], $list);
            }
        }
    }
}
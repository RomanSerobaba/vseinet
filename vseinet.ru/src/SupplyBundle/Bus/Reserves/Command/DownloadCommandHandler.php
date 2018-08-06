<?php 

namespace SupplyBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Component\OrderComponent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DownloadCommandHandler extends MessageHandler
{
    public function handle(DownloadCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->find($command->id);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException();
        }

        $spreadsheet = null;
        $data = '';
        $component = new OrderComponent($em);
        $supplierProducts = $component->getDownloadingProducts((int)$command->id, $command->pointId, $command->withConfirmedReserves);

        if ($command->format === DownloadCommand::FORMAT_STRING) {
            $data .= '
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
    </head>
    <body>
        <div style="word-wrap: break-word;">
            ';
            $i=1;
            foreach($supplierProducts as $product) {
                if($product['quantity']>1)
                    $attr = " title='".$product['quantity']."' style='color: red'";
                else
                    $attr = '';
                $data .= "<span".$attr.">".$product['code']."</span>";
                if($i!=count($supplierProducts)) $data .= ",";
                $i++;
            }
            $data .= "</div></body>";
        } elseif ($command->format === DownloadCommand::FORMAT_HTML) {
            $titles = [];
            if($command->isExportCode) {
                $titles[] = ["name" => "Код", "code" => "code",];
            }
            if($command->isExportName) {
                $titles[] = ["name" => "Наименование", "code" => "name",];
            }
            if($command->isExportQuantity) {
                $titles[] = ["name" => "Количество", "code" => "quantity",];
            }
            if($command->isExportPrice) {
                $titles[] = ["name" => "Цена", "code" => "price",];
            }

            $data .= '
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width,initial-scale=1" /
    </head>
    <body>';
            $data .= "<table cellpadding='5' border='1' style='font-size: 11px; border-collapse: collapse;'><thead><tr>";
            foreach($titles as $title) {
                $data .= "<th>".$title['name']."</th>";
            }
            $data .= "</tr></thead>";
            if(!empty($supplierProducts)) {
                $data .= "<tbody>";
                foreach($supplierProducts as $product) {
                    $data .= "<tr>";
                    foreach($titles as $title) {
                        $data .= "<td>".$product[$title['code']]."</td>";
                    }
                    $data .= "</tr>";
                }
                $data .= "</tbody>";
            }
            $data .= "</table>";
            $data .= "</body>";
        } elseif ($command->format === DownloadCommand::FORMAT_CSV) {
            ob_end_clean();
            $columns = [];

            $index = 0;
            foreach ($supplierProducts as $p) {
                $row = [];
                if($command->isExportCode) {
                    if ($index == 0) {
                        $columns['code'] = 'Код';
                    }
                    $row[] = $p['code'];
                }
                if($command->isExportName) {
                    if ($index == 0) {
                        $columns['name'] = 'Наименование';
                    }
                    $row[] = $p['name'];
                }
                if($command->isExportQuantity) {
                    if ($index == 0) {
                        $columns['quantity'] = 'Количество';
                    }
                    $row[] = $p['quantity'];
                }
                if($command->isExportPrice) {
                    if ($index == 0) {
                        $columns['price'] = 'Цена';
                    }
                    $row[] = $p['price'];
                }

                if ($index == 0 && $columns) {
                    $data .= iconv('utf-8','cp1251', implode(';', $columns)."\r\n");
                }

                if ($row) {
                    $data .= iconv('utf-8', 'cp1251', implode(';', $row) . "\r\n");
                }

                $index++;
            }
        } elseif ($command->format === DownloadCommand::FORMAT_EXCEL) {
            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('Vseinet.ru')
                ->setTitle('Vseinet.ru')
                ->setSubject('Office 2007 XLSX Document');

            $spreadsheet->setActiveSheetIndex(0);

            $titles = [];
            if($command->isExportCode) {
                $titles[] = array("name" => "Код", "code" => "code");
            }
            if($command->isExportName) {
                $titles[] = array("name" => "Наименование", "code" => "name");
            }
            if($command->isExportQuantity) {
                $titles[] = array("name" => "Количество", "code" => "quantity");
            }
            if($command->isExportPrice) {
                $titles[] = array("name" => "Цена", "code" => "price");
            }

            for ($i = 65; $i < 65 + count($titles); $i++)
                $spreadsheet->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);

            foreach ($titles as $key => $title) {
                $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $key) . '1', $title['name']);
            }

            for ($i = 65; $i < 65 + count($titles); $i++) {
                $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFont()->getColor()->setRGB('FFFFFF');
                $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFill()->setFillType(Fill::FILL_SOLID);
                $spreadsheet->getActiveSheet()->getStyle(chr($i) . '1')->getFill()->getStartColor()->setRGB('222222');
            }

            $num = 2;
            foreach ($supplierProducts as $p) {
                foreach ($titles as $key => $title) {
                    switch ($title['name']) {
                        case 'Код':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $key) . $num, html_entity_decode($p['code'], ENT_QUOTES));
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $key) . $num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;

                        case 'Наименование':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $key) . $num, html_entity_decode($p['name'], ENT_QUOTES));
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $key) . $num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                            break;

                        case 'Количество':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $key) . $num, round($p['quantity']));
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $key) . $num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;

                        case 'Цена':
                            $spreadsheet->getActiveSheet()->setCellValue(chr(65 + $key) . $num, $p['price']/100);
                            $spreadsheet->getActiveSheet()->getStyle(chr(65 + $key) . $num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;
                    }
                }

                $num++;
            }
            $spreadsheet->getActiveSheet()->calculateColumnWidths();
        }

        if (file_exists($command->fileName)) {
            unlink($command->fileName);
        }

        if ($command->format === DownloadCommand::FORMAT_EXCEL) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save($command->fileName);
        } else {
            file_put_contents($command->fileName, $data);
        }

        chmod($command->fileName, 0777);
    }
}
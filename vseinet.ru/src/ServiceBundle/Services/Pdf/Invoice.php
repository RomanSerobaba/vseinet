<?php

namespace ServiceBundle\Services\Pdf;

use ServiceBundle\Components\Number;
use ServiceBundle\Components\ViPdf;

class Invoice extends ViPdf
{
    protected $orderInvoice = [];
    protected $lastPageFlag = false;

    /**
     * Invoice constructor.
     *
     * @param array  $orderInvoice
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @param bool   $unicode
     * @param string $encoding
     * @param bool   $diskcache
     * @param bool   $pdfa
     */
    public function __construct(
        array $orderInvoice,
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->orderInvoice = $orderInvoice;
        $this->SetAutoPageBreak(true, 35);
    }

    public function Close()
    {
        $this->lastPageFlag = true;
        $this->Close();
    }

    public function Header()
    {
        $this->SetXY(10, 10);
        $this->setImageScale(1.5);
        $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/logo/min.png', $this->GetX(),
            $this->GetY(), 0, 0, 'PNG');
        $this->SetXY($this->GetX() + 17, $this->GetY() + 1);
        $this->SetFont('Intro', '', 22);
        $this->Cell(0, 0, 'Vseinet.ru', 0, 1);
        $this->SetXY(165, 75);
        $this->PrintQrcode($this->orderInvoice['number'], $this->GetX(), $this->GetY());
    }

    /**
     * @TODO доделать вставку баннеров
     */
    public function Footer()
    {
        if ($this->lastPageFlag) {
            $this->SetY(-90);
//            $banner = Model::factory('Banner')->getOneRandom('main', 0);
//            $banner_html = '';
//            $this->SetXY(35, -60);
//            $this->setImageScale(2.5);
//            $this->Image($this->getParameter('project.web.path').'/u/b/main/image_'.$banner['id'].'.jpg', $this->GetX(), $this->GetY(), 0, 0, 'JPG');
//
//            if($banner['title']) {
//                $banner_html .= '<p><h2 style="font-size: 28px;">'.$banner['title'].'</h2></p>';
//            }
//
//            if($banner['text']) {
//                $banner_html .= '<p style="font-size: 20px;">'.$banner['text'].'</p>';
//            }
//
//            if($banner['text2']) {
//                if($banner['text2_RUR']) {
//                    $banner_html .= '<p style="font-size: 20px;"><b>'.$banner['text2'].' Р</b></p>';
//                } else {
//                    $banner_html .= '<p style="font-size: 20px;"><b>'.$banner['text2'].'</b></p>';
//                }
//            }
//            $banner_html = '<table><tr><td width="15%"></td><td width="35%">'.$banner_html.'</td></tr></table>';
//            $this->SetXY(10, -60);
//            $this->SetFont('tinos', '', 8);
//            $this->WriteHTML($banner_html, true, false, true, false, '');

            $this->SetY(-75);
            $this->SetFont('tinos', '', 8);

            $this->WriteHTML($this->orderInvoice['footer'], true, false, true, false, '');
            if (!empty($this->orderInvoice['with_stamp'])) {
                if ($this->orderInvoice['seller_tin'] === self::SOKOLOV_TIN) {
                    $this->setImageScale(5.5);
                    $this->SetXY(140, -80);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::SOKOLOV_TIN . '_signature.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                    $this->SetXY(152, -101);
                    $this->setImageScale(3.5);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::SOKOLOV_TIN . '_stamp.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                } else {
                    $this->setImageScale(4.5);
                    $this->SetXY(145, -80);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::MOKIEVSKAYA_TIN . '_signature.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                    $this->SetXY(157, -102);
                    $this->setImageScale(3.5);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::MOKIEVSKAYA_TIN . '_stamp.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                }
            }
        } else {
            $this->SetY(-15);
            $this->SetFont('tinos', '', 8);

            $this->WriteHTML($this->orderInvoice['footer'], true, false, true, false, '');
            if (!empty($this->orderInvoice['with_stamp'])) {
                if ($this->orderInvoice['seller_tin'] === self::SOKOLOV_TIN) {
                    $this->setImageScale(5.5);
                    $this->SetXY(140, -20);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::SOKOLOV_TIN . '_signature.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                    $this->SetXY(152, -41);
                    $this->setImageScale(3.5);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::SOKOLOV_TIN . '_stamp.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                } else {
                    $this->setImageScale(4.5);
                    $this->SetXY(145, -20);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::MOKIEVSKAYA_TIN . '_signature.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                    $this->SetXY(157, -42);
                    $this->setImageScale(3.5);
                    $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::MOKIEVSKAYA_TIN . '_stamp.png',
                        $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                }
            }
        }

        if (empty($this->pagegroups)) {
            $pageNoAlias = $this->getAliasNumPage();
        } else {
            $pageNoAlias = $this->getPageNumGroupAlias();
        }

        $this->SetXY(135, 5);
        $this->WriteHTML("Счет №" . $this->orderInvoice['number'] . " от " . Number::rusMonthDate("d M Y",
                strtotime($this->orderInvoice['date'])) . ", стр. " . $pageNoAlias, true, false, true, false, '');
    }
}
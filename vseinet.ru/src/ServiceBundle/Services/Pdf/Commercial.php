<?php

namespace ServiceBundle\Services\Pdf;

use ServiceBundle\Components\Number;
use ServiceBundle\Components\ViPdf;

class Commercial extends ViPdf
{
    protected $orderCommercial;

    public function __construct(
        array $orderCommercial,
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->orderCommercial = $orderCommercial;
        $this->SetMargins(15, 15, 15, true);
        $this->SetAutoPageBreak(true, 35);
    }


    public function Header()
    {
        $this->SetXY(16, 10);
        $this->setImageScale(1.5);
        $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/logo/min.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
        $this->SetXY($this->GetX() + 17, $this->GetY() + 1);
        $this->SetFont('Intro', '', 22);
        $this->Cell(0, 0, 'Vseinet.ru', 0, 1);
    }

    public function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('tinos', '', 8);
        $this->WriteHTML($this->orderCommercial['footer'], true, false, true, false, '');
        if (!empty($this->orderCommercial['with_stamp'])) {
            if ($this->orderCommercial['seller_tin'] === self::SOKOLOV_TIN) {
                $this->setImageScale(5.5);
                $this->SetXY(140, -25);
                $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::SOKOLOV_TIN . '_signature.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                $this->SetXY(152, -51);
                $this->setImageScale(3.5);
                $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::SOKOLOV_TIN . '_stamp.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
            } else {
                $this->setImageScale(4.5);
                $this->SetXY(145, -25);
                $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::MOKIEVSKAYA_TIN . '_signature.png', $this->GetX(), $this->GetY(), 0, 0,
                    'PNG');
                $this->SetXY(157, -50);
                $this->setImageScale(3.5);
                $this->Image($this->getContainer()->getParameter('project.web.images.path') . '/document/' . self::MOKIEVSKAYA_TIN . '_stamp.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
            }
        }
        if (empty($this->pagegroups)) {
            $pageNoAlias = $this->getAliasNumPage();
        } else {
            $pageNoAlias = $this->getPageNumGroupAlias();
        }
        $this->SetXY(10, -9);
        $this->WriteHTML($pageNoAlias, true, false, true, false, '');
    }
}
<?php

namespace ServiceBundle\Services\Pdf;

use ServiceBundle\Components\Number;
use ServiceBundle\Components\ViPdf;

class Contract extends ViPdf
{
    protected $orderContract = [];

    /**
     * Contract constructor.
     *
     * @param array  $orderContract
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @param bool   $unicode
     * @param string $encoding
     * @param bool   $diskcache
     * @param bool   $pdfa
     */
    public function __construct(
        array $orderContract,
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->orderContract = $orderContract;
        $this->SetAutoPageBreak(true, 35);
        $this->SetMargins(15, 15, 15, true);
    }

    public function Header()
    {
    }

    public function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('tinos', '', 8);
        $this->WriteHTML($this->orderContract['footer'], true, false, true, false, '');
        if (!empty($this->orderContract['with_stamp'])) {
            if ($this->orderContract['seller_tin'] === self::SOKOLOV_TIN) {
                $this->setImageScale(5.5);
                $this->SetXY(43, -17);
                $this->Image($this->getContainer()->getParameter('project.web.images.path').'/document/'.self::SOKOLOV_TIN.'_signature.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
                $this->SetXY(7, -42);
                $this->setImageScale(3.5);
                $this->Image($this->getContainer()->getParameter('project.web.images.path').'/document/'.self::SOKOLOV_TIN.'_stamp.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
            } else {
                $this->setImageScale(4.5);
                $this->SetXY(45, -18);
                $this->Image($this->getContainer()->getParameter('project.web.images.path').'/document/'.self::MOKIEVSKAYA_TIN.'_signature.png', $this->GetX(), $this->GetY(), 0, 0,
                    'PNG');
                $this->SetXY(7, -42);
                $this->setImageScale(3.5);
                $this->Image($this->getContainer()->getParameter('project.web.images.path').'/document/'.self::MOKIEVSKAYA_TIN.'_stamp.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
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
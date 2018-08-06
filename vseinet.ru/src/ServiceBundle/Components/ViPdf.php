<?php
namespace ServiceBundle\Components;

use AppBundle\Bus\Message\MessageHandler;
use \TCPDF as pdf;

class ViPdf extends pdf
{
    const SOKOLOV_TIN = '583410262137';
    const MOKIEVSKAYA_TIN = '583502363760';

    /**
     * @var MessageHandler
     */
    protected $container;

    /**
     * @return MessageHandler
     */
    public function getContainer(): MessageHandler
    {
        return $this->container;
    }

    /**
     * @param MessageHandler $container
     */
    public function setContainer(MessageHandler $container)
    {
        $this->container = $container;
    }

    /**
     * ViPdf constructor.
     *
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @param bool   $unicode
     * @param string $encoding
     * @param bool   $diskcache
     * @param bool   $pdfa
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        $this->SetMargins(10, 10, 10, true);
        $this->SetAutoPageBreak(true, 10);

        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('vseinet.ru (mail@vseinet.ru)');

        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $this->SetCellPadding(0);

        $this->SetLineWidth(0.1);

        // $this->setFontSubsetting(false);

        $this->SetProtection(['modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble']);
    }
    
    public function SetXY($x, $y, $rtloff = false)
    {
        if ($x !== null) {
            $this->SetX($x, $rtloff);
        }
        if ($y !== null) {
            $this->SetY($y, false, $rtloff);
        }
    }

    public function PrintQrcode($orderId, $x = null, $y = null)
    {
        $this->SetXY($x, $y);
        $this->Image($this->getContainer()->getParameter('pdf.qrcodes.path') . DIRECTORY_SEPARATOR . $orderId . '.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
    }

    public function PrintLogo($x = null, $y = null)
    {
        $this->SetXY($x, $y);
        $this->Image($this->getContainer()->getParameter('project.web.images.path').'/logo/min.png', $this->GetX(), $this->GetY(), 0, 0, 'PNG');
        $this->SetXY($this->GetX() + 18, $this->GetY() + 3);
        $this->SetFont('Intro', '', 22);
        $this->Cell(0, 0, 'Vseinet.ru', 0, 1);

        return $this->GetStringWidth('Vseinet.ru') + 18;
    }

    public function PrintSeller($seller, $width, $align = 'L')
    {
        $this->SetFont('dejavusanscondensed', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $text = "Продавец: {$seller['name']}\nИНН {$seller['tin']}; ОГРН {$seller['ogrn']}\nТел.: {$seller['phone']}\nАдрес: {$seller['address']}";
        $this->MultiCell($width, 0, $text, 0, $align, false, 0);
    }

    public function adjustHeightRow(array $widths, array $data)
    {
        $height = 0;
        foreach ($widths as $index => $width) {
            $height = max($height, $this->GetStringHeight($width, $data[$index]));
        }

        return $height;
    }

    public function PrintTableHeadRow(array $widths, array $data, $align = 'C')
    {   
        $height = $this->adjustHeightRow($widths, $data);
        foreach ($widths as $index => $width) {
            $this->MultiCell($width, $height, $data[$index], 1, isset($align[$index]) ? $align[$index] : 'L', false, 0, '', '', true, 0, false, true, $height, 'M');
        }
        $this->Ln();
    }

    public function PrintTableBodyRow(array $widths, $height, array $data, array $align = null, $border = 1, $valign = 'M', $stretch = 0)
    {
        $height = $height ?: $this->adjustHeightRow($widths, $data);
        foreach ($widths as $index => $width) {
            $this->MultiCell($width, $height, $data[$index], $border, isset($align[$index]) ? $align[$index] : 'L', false, 0, '', '', true, 0, false, true, $height, $valign);
        }
        $this->Ln();
    }

    /**
     * @param      $signature
     * @param      $width
     * @param null $x
     * @param null $y
     * @param int  $size
     */
    public function PrintSignature($signature, $width, $x = null, $y = null, $size = 7)
    {
        $this->SetXY($x, $y);
        $this->SetFont('dejavusanscondensed', '', $size);
        $w = $this->GetStringWidth($signature) + 1;
        $h = $this->GetStringHeight($w, $signature);
        $this->Cell($w, $h, $signature, 0, false);
        $this->Line($this->GetX(), $this->GetY() + $h, $this->GetX() + $width - $w, $this->GetY() + $h);
    }

    public function PrintUnderSignature($signature, $width, $x = null, $y = null, $size = 6, $value = '')
    {
        if ($value) {
            $this->SetXY($x, $y);
            $this->SetFont('dejavusanscondensed', '', 9);
            $this->Cell($width, 5, $value, 0, 2, 'C');
            $this->Ln(1);
        }
        $y += 5;
        $this->SetXY($x, $y);
        $this->Line($this->GetX(), $this->GetY()+1, $this->GetX() + $width, $this->GetY()+1);
        $this->Ln(1);
        $this->SetFont('dejavusanscondensed', '', $size);
        $this->SetXY($x, $this->GetY() + 2);
        if (!empty($signature)) {
            $this->Cell($width, 2, $signature, 0, false, 'C');
        }
    }

    public function Header()
    {
        if ($this->print_header) {
            $this->SetY(0);
            $this->SetFont('dejavusanscondensed', '', 7);
            if (empty($this->pagegroups)) {
                $pageNo = $this->pageNo();
                $pageNoAlias = $this->getAliasNumPage().'/'.$this->getAliasNbPages();
            }
            else {
                $pageNo = $this->getGroupPageNo();
                $pageNoAlias = $this->getPageNumGroupAlias().'/'.$this->getPageGroupAlias();
            }
            if ($pageNo > 1) {
                if ($this->title) {
                    $this->Cell(0, 10, $this->title, 0, false, 'L');
                }
            }
            $this->Cell(0, 10, $this->getAliasRightShift().'страница '.$pageNoAlias, 0, false, 'R');
        }
    }

    public function Footer()
    {

    }
} // End VIPDF
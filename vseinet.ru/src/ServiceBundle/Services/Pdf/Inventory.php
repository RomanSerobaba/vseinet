<?php

namespace ServiceBundle\Services\Pdf;

use ServiceBundle\Components\Number;
use ServiceBundle\Components\ViPdf;

class Inventory extends ViPdf
{
    protected $inventory = [];
    protected $orderContract = [];

    /**
     * Contract constructor.
     *
     * @param array  $inventory
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @param bool   $unicode
     * @param string $encoding
     * @param bool   $diskcache
     * @param bool   $pdfa
     */
    public function __construct(
        array $inventory,
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->SetAutoPageBreak(true, 5);
        $this->inventory = $inventory;
        $this->SetMargins(5, 5, 5, true);
    }

    public function Header()
    {
        $this->SetXY(5, 5);

        $this->SetFont('dejavusanscondensed', '', 8);
        $this->WriteHTML('Инвентаризация № '.$this->inventory['number'].' от '.$this->inventory['created_at'], true, false, true, false, '');
    }

    public function Footer()
    {
    }
}
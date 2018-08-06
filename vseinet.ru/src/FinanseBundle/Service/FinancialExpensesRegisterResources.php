<?php

namespace FinanseBundle\Service;

class FinancialExpensesRegisterResources
{

    private $delta;

    public function setDelta(int $delta)
    {
        $this->delta = $delta;
    }

    public function getDelta()
    {
        return $this->delta;
    }

    public function __construct(int $delta)
    {
        $this->delta = $delta;
    }

}

<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\Specification;

use AppBundle\Enum\ProductAvailabilityCode;

class Suppliers
{
    public function isActive()
    {
        return "sp.isHidden = false AND sc.isHidden = false";
    }

    public function isHidden()
    {
        return "(sp.isHidden = true OR sc.isHidden = true)";    
    }
 
    public function filterByState($filter)
    {
        switch ($filter) {
            case 'active':
                return " AND ".$this->isActive();

            case 'hidden':
                return " AND ".$this->isHidden();
        }

        return "";
    }

    public function isAvalability()
    {
        return "sp.availabilityCode > '".ProductAvailabilityCode::OUT_OF_STOCK."'";
    }

    public function build($filter)
    {
        return $this->filterByState($filter)." AND ".$this->isAvalability();
    }
}
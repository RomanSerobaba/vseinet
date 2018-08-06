<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\Specification;

class Catalog
{
    public function isNew()
    {
        return "bp.isHidden = false AND bp.updatedAt IS NULL";
    }

    public function isActive()
    {
        return "bp.isHidden = false AND TIMESTAMPDIFF(DAY, COALESCE(bp.updatedAt, CURRENT_TIMESTAMP()), CURRENT_TIMESTAMP()) <= 90";   
    }

    public function isOld()
    {
        return "bp.isHidden = false AND TIMESTAMPDIFF(DAY, bp.updatedAt, CURRENT_TIMESTAMP()) > 90";    
    }

    public function isHidden()
    {
        return "bp.isHidden = true";   
    }

    public function filterByState($filter)
    {
        switch ($filter) {
            case 'new':
                return " AND ".$this->isNew();

            case 'active':
                return " AND ".$this->isActive();    

            case 'old':
                return " AND ".$this->isOld();

            case 'hidden':
                return " AND ".$this->isHidden();
        }

        return "";    
    }

    public function filterByCity($cityId)
    {
        if (null === $cityId) {
            return " AND p.geoCityId IS NULL";
        }

        return " AND (p.geoCityId IS NULL OR p.geoCityId = {$cityId})";
    }

    public function build($filter, $cityId)
    {
        return $this->filterByState($filter).$this->filterByCity($cityId);
    }
}
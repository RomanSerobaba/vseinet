<?php 

namespace FinanseBundle\Bus\Equipment\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class FoundResultsQueryHandler extends MessageHandler
{
    public function handle(FoundResultsQuery $query)
    {
        
        $orFilters = [];
        if ($query->withCar) $orFilters[] = 'eq.is_car';
        if ($query->withProduct) $orFilters[] = 'eq.base_product_id is not null';

        if (!empty($orFilters)) {
            $orFilterText = "and (". implode(" or ", $orFilters) .")";
        }else{
            $orFilterText = "";
        }
        
        $q = '%'. mb_strtolower($query->q) .'%';
            
        $queryText = "
            select
                eq.id,
                case
                    when eq.reg_number is not null then concat(eq.name, ' (', eq.reg_number, ')')
                    when eq.base_product_id is not null then concat(eq.name, ' (', bp.name, ')')
                    else eq.name
                    end as name
            from equipment eq
            left join base_product bp on bp.id = eq.base_product_id
            where
                (
                    lower(eq.name) like :q or 
                    lower(eq.reg_number) like :q or
                    lower(bp.name) like :q
                )
                {$orFilterText}
            limit {$query->limit}
        ";
        
        return $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\EquipmentDTO::class, DTORSM::ARRAY_INDEX))                
                ->setParameters(['q' => $q])
                ->getResult('DTOHydrator');

    }

}
<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetSuppliersQueryHandler extends MessageHandler
{
    public function handle(GetSuppliersQuery $query)
    {
        $where = "";
        if ('active' == $query->filter) {
            $codes = array_map(function($parser) {
                return str_replace('Strategy.php', '', basename($parser));
            }, glob(dirname(__DIR__).'/Parser/Strategy/*.php'));
            
            $where = " AND spl.isActive = true AND s.code IN ('".implode("','", $codes)."')";            
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierPricelist\Query\DTO\Supplier (
                    s.id,
                    s.code
                )    
            FROM SupplyBundle:Supplier s 
            INNER JOIN SupplyBundle:SupplierPricelist spl WITH spl.supplierId = s.id 
            WHERE s.isActive = true {$where}
            GROUP BY s.id
            ORDER BY s.code
        ");

        return $q->getArrayResult();
    }
}

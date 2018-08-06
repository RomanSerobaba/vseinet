<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetLoadedQueryHandler extends MessageHandler
{
    public function handle(GetLoadedQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierPricelist\Query\DTO\Loaded (
                    spl.id,
                    s.code,
                    spl.name,
                    spl.uploadStartedAt 
                )
            FROM SupplyBundle:Supplier s 
            INNER JOIN SupplyBundle:SupplierPricelist spl WITH spl.supplierId = s.id 
            WHERE spl.uploadStartedAt IS NOT NULL 
            ORDER BY s.code, spl.name 
        ");

        return $q->getArrayResult();
    }
}

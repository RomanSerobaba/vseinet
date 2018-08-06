<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetSuppliersQueryHandler extends MessageHandler
{
    public function handle(GetSuppliersQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $spec = new Specification\Suppliers();
        $where = $spec->build($query->filter);

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\Supplier (
                    s.id,
                    s.code,
                    COUNT(sp.id)
                )
            FROM SupplyBundle:Supplier s 
            INNER JOIN SupplyBundle:SupplierCategory sc WITH sc.supplierId = s.id AND sc.pid IS NULL 
            INNER JOIN SupplyBundle:SupplierCategoryPath scp WITH scp.pid = sc.id
            INNER JOIN SupplyBundle:SupplierProduct sp WITH sp.categoryId = scp.id 
            WHERE sp.baseProductId IS NULL {$where}
            GROUP BY s.id
            ORDER BY s.code 
        ");

        return $q->getArrayResult();
    }
}
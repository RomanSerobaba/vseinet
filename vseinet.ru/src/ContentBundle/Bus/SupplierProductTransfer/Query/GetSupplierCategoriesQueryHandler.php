<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierCategory;

class GetSupplierCategoriesQueryHandler extends MessageHandler
{
    public function handle(GetSupplierCategoriesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->find($query->supplierId);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException(sprintf('Поставщик %d не найден', $query->supplierId));
        }

        if (0 === $query->pid) {
            $parent = new SupplierCategory();
        }
        else {
            $parent = $this->getDoctrine()->getManager()->getRepository(SupplierCategory::class)->find($query->pid);
            if (!$parent instanceof SupplierCategory) {
                throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->pid));
            }
        }

        $spec = new Specification\Suppliers();
        $where = $spec->build($query->filter);

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\SupplierCategory (
                    sc.id,
                    COALESCE(sc.pid, 0),
                    sc.supplierId,
                    sc.name,
                    sc.syncCategoryId, 
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM SupplyBundle:SupplierCategory scc 
                        WHERE scc.pid = sc.id
                    ) 
                    THEN false ELSE true END,
                    sc.isHidden,
                    COUNT(sp.id)
                )
            FROM SupplyBundle:SupplierCategory sc
            INNER JOIN SupplyBundle:SupplierCategoryPath scp WITH scp.pid = sc.id
            INNER JOIN SupplyBundle:SupplierProduct sp WITH sp.categoryId = scp.id 
            WHERE sc.supplierId = :supplierId AND COALESCE(:pid, 0) = COALESCE(sc.pid, 0) AND sp.baseProductId IS NULL {$where}
            GROUP BY sc.id
            ORDER BY sc.name 
        ");
        $q->setParameter('supplierId', $supplier->getId());
        $q->setParameter('pid', $parent->getId());

        return $q->getArrayResult();
    }
}

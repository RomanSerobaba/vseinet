<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierCategory;

class GetSupplierProductsQueryHandler extends MessageHandler
{
    public function handle(GetSupplierProductsQuery $query)
    {
        $category = $this->getDoctrine()->getManager()->getRepository(SupplierCategory::class)->find($query->categoryId);
        if (!$category instanceof SupplierCategory) {
            throw new NotFoundHttpException(sprintf('Категория поставщика %d не найдена', $query->categoryId));
        }

        $spec = new Specification\Suppliers();
        $where = $spec->build($query->filter);

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\SupplierProduct (
                    sp.id,
                    sp.name,
                    sp.categoryId,
                    COALESCE(sp.code, sp.article),
                    sp.availabilityCode,
                    sp.price,
                    b.name,
                    sp.description,
                    sp.isHidden,
                    GROUP_CONCAT(spbc.barCode SEPARATOR ', ')
                )
            FROM SupplyBundle:SupplierProduct sp 
            INNER JOIN SupplyBundle:SupplierCategory sc WITH sc.id = sp.categoryId
            LEFT OUTER JOIN ContentBundle:Brand b WITH b.id = sp.brandId
            LEFT OUTER JOIN SupplyBundle:SupplierProductBarCode spbc WITH spbc.productId = sp.id
            WHERE sp.categoryId = :categoryId AND sp.baseProductId IS NULL {$where}
            GROUP BY sp.id, b.id
            ORDER BY sp.name 
        ");
        $q->setParameter('categoryId', $category->getId());

        return $q->getArrayResult();
    }
}

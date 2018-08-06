<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;

class GetSupplierProductsQueryHandler extends MessageHandler
{
    public function handle(GetSupplierProductsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $query->id));
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\BaseProduct\Query\DTO\SupplierProduct (
                    sp.id,
                    s.code,
                    sp.name,
                    pp.url
                )
            FROM SupplyBundle:SupplierProduct sp
            INNER JOIN SupplyBundle:Supplier s WITH s.id = sp.supplierId  
            LEFT OUTER JOIN ContentBundle:ParserProduct pp WITH pp.supplierProductId = sp.id
            WHERE sp.baseProductId = :baseProductId
            ORDER BY s.code, sp.name 
        ");
        $q->setParameter('baseProductId', $product->getId());

        return $q->getArrayResult();
    }
}

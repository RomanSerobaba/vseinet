<?php 

namespace SupplyBundle\Bus\LowCostPurchases\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\ORM\Query\DTORSM;

class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW SupplyBundle\Bus\LowCostPurchases\Query\DTO\Products (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    c.name,
                    p.id,
                    bp.supplierPrice,
                    sp.supplierId,
                    s1.code,
                    sp.price,
                    s2.code,
                    ( ( sp.price - bp.supplierPrice ) / ( ( sp.price + bp.supplierPrice ) / 2 ) * 100 )
                )
            FROM
                ContentBundle:BaseProduct AS bp
                INNER JOIN ContentBundle:Category AS c WITH bp.categoryId = c.id
                INNER JOIN PricingBundle:Product AS p WITH bp.id = p.baseProductId
                INNER JOIN SupplyBundle:SupplierProduct sp WITH sp.baseProductId = bp.id 
                    AND sp.supplierId <> bp.supplierId 
                    AND sp.price > 0 
                    AND sp.availabilityCode = :available
                LEFT JOIN SupplyBundle:SupplierProduct sp2 WITH sp2.baseProductId = bp.id 
                    AND sp2.supplierId <> bp.supplierId
                    AND sp2.price > 0 
                    AND sp2.availabilityCode = :available
                    AND ( sp.price > sp2.price OR sp.price = sp2.price AND sp.id < sp2.id )
                INNER JOIN SupplyBundle:Supplier AS s1 WITH bp.supplierId = s1.id
                INNER JOIN SupplyBundle:Supplier AS s2 WITH sp.supplierId = s2.id 
            WHERE
                bp.supplierAvailabilityCode = :available
                AND bp.supplierPrice > 0 
                AND sp2.id IS NULL AND bp.categoryId = :category_id
        ');

        $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
        $q->setParameter('category_id', $query->categoryId);

        return $q->getResult();
    }
}
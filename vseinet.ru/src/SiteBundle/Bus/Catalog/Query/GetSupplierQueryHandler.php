<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetSupplierQueryHandler extends MessageHandler
{
    public function handle(GetSupplierQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Catalog\Query\DTO\Supplier (
                    s.id, 
                    s.code
                )
            FROM SupplyBundle:Supplier AS s 
            WHERE LOWER(s.code) = LOWER(:code) AND s.isActive = true
        ");
        $q->setParameter('code', $query->code);
        $supplier = $q->getOneOrNullResult();

        if (!$supplier instanceof DTO\Supplier) {
            throw new NotFoundHttpException();
        }

        return $supplier;
    }
}

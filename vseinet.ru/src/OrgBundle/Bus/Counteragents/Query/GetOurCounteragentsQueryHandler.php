<?php 

namespace OrgBundle\Bus\Counteragents\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;


class GetOurCounteragentsQueryHandler extends MessageHandler
{
    public function handle(GetOurCounteragentsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW OrgBundle\Bus\Counteragents\Query\DTO\OurCounteragents (
                    os.counteragentId,
                    os.shortName
                )
            FROM
                SupplyBundle:SupplierReserve AS sr
                LEFT JOIN SupplyBundle:SupplierReserve AS ssr WITH ssr.supplierId = sr.supplierId
                    AND ssr.isShipping = TRUE
                    AND ssr.closedAt IS NULL
                JOIN SupplyBundle:SupplierReserveRegister AS srr WITH COALESCE ( ssr.id, sr.id ) = srr.supplierReserveId
                JOIN OrderBundle:OrderItem AS oi WITH oi.id = srr.orderItemId
                JOIN OrderBundle:OrderTable AS o WITH o.id = oi.orderId
                JOIN AccountingBundle:OurSeller AS os WITH os.counteragentId = o.ourSellerCounteragentId
            WHERE
                sr.supplierId = :supplier_id
                AND srr.supplyId IS NULL
                AND sr.isShipping = FALSE
                AND sr.closedAt IS NULL
            GROUP BY
                os.counteragentId
            HAVING
                SUM( srr.delta ) > 0
            ORDER BY
                SUM( srr.delta ) DESC
        ');

        $q->setParameter('supplier_id', $query->supplierId);

        return $q->getResult();
    }
}
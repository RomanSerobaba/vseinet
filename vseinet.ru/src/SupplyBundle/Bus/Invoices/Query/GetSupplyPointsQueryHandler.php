<?php
namespace SupplyBundle\Bus\Invoices\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetSupplyPointsQueryHandler extends MessageHandler
{
    /**
     * @param GetSupplyPointsQuery $query
     *
     * @return array
     */
    public function handle(GetSupplyPointsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW SupplyBundle\Bus\Invoices\Query\DTO\SupplyPoints (
                    gp.id,
                    gp.name
                )
            FROM
                SupplyBundle:SupplierReserveRegister AS srr
                JOIN OrderBundle:OrderItem AS oi WITH oi.id = srr.orderItemId
                JOIN OrderBundle:OrderTable AS o WITH o.id = oi.orderId
                JOIN SupplyBundle:ViewGeoPoint AS gp WITH gp.id = o.geoPointId
                JOIN SupplyBundle:Supply AS s WITH s.id = srr.supplyId 
            WHERE
                srr.supplyId = :supply_id 
                AND gp.id != s.destinationPointId 
            GROUP BY
                gp.name,
                gp.id 
            HAVING
                SUM( srr.delta ) > 0
        ");

        $q->setParameter('supply_id', $query->id);

        return $q->getResult();
    }
}
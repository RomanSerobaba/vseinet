<?php 

namespace DeliveryBundle\Bus\Request\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GeoBundle\Entity\GeoPoint;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $clause = '';
        
        if ($query->isFree) {
            $clause .= " AND di.id IS NULL";
        }
        if ($query->deliveryNumber) {
            $clause .= " AND di.deliveryId = :deliveryId";
        }
        if ($query->type) {
            $clause .= " AND od.type = :type";
        }
        if ($query->pointId) {
            $point = $this->getDoctrine()->getManager()->getRepository(GeoPoint::class)->find($query->pointId);
            $clause .= " AND gc.id = :cityId";
        }
        if ($query->transportCompanyId) {
            $clause .= " AND od.freightOperatorId = :transportCompanyId";
        }
        
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                COUNT(od.id)
            FROM DeliveryBundle:OrderDelivery AS od
            LEFT JOIN DeliveryBundle:DeliveryItem AS di WITH di.orderDeliveryId = od.id
            JOIN DeliveryBundle:OrderDeliveryItem AS odi WITH odi.orderDeliveryId = od.id
            JOIN OrderBundle:OrderItem AS oi WITH oi.id = odi.orderItemId
            JOIN OrderBundle:OrderDoc AS o WITH o.number = oi.orderId
            JOIN GeoBundle:GeoCity AS gc WITH gc.id = o.geoCityId
            WHERE 1 = 1{$clause}
        ");
        if ($query->deliveryNumber) {
            $q->setParameter('deliveryId', $query->deliveryNumber);
        }
        if ($query->type) {
            $q->setParameter('type', $query->type);
        }
        if ($query->pointId) {
            $q->setParameter('cityId', $point->getGeoCityId());
        }
        if ($query->transportCompanyId) {
            $q->setParameter('transportCompanyId', $query->transportCompanyId);
        }
        $total = $q->getSingleScalarResult();

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW DeliveryBundle\Bus\Request\Query\DTO\Request(
                    od.id,
	                CONCAT('Заявка на доставку от ', DATE_FORMAT(od.createdAt, '%d.%m.%Y')),
                    ga.address,
                    CONCAT_WS(' ', p.lastname, p.firstname, p.secondname),
                    gc.name,
                    od.needLifting,
                    od.desiredDatetime,
                    od.cost,
                    od.liftingCost
                )
            FROM DeliveryBundle:OrderDelivery AS od
            LEFT JOIN DeliveryBundle:DeliveryItem AS di WITH di.orderDeliveryId = od.id
            LEFT JOIN ThirdPartyBundle:GeoAddress AS ga WITH ga.id = od.geoAddressId
            JOIN DeliveryBundle:OrderDeliveryItem AS odi WITH odi.orderDeliveryId = od.id
            JOIN OrderBundle:OrderItem AS oi WITH oi.id = odi.orderItemId
            JOIN OrderBundle:OrderDoc AS o WITH o.number = oi.orderId
            JOIN OrderBundle:ClientOrder AS co WITH co.orderId = o.number
            LEFT JOIN AppBundle:User AS u WITH u.id = co.userId
            LEFT JOIN AppBundle:Person AS p WITH p.id = u.personId
            JOIN GeoBundle:GeoCity AS gc WITH gc.id = o.geoCityId
            WHERE 1 = 1{$clause}
            GROUP BY ga.id, od.id, p.id, gc.id
        ");
        if ($query->deliveryNumber) {
            $q->setParameter('deliveryId', $query->deliveryNumber);
        }
        if ($query->type) {
            $q->setParameter('type', $query->type);
        }
        if ($query->pointId) {
            $q->setParameter('cityId', $point->getGeoCityId());
        }
        if ($query->transportCompanyId) {
            $q->setParameter('transportCompanyId', $query->transportCompanyId);
        }
        $q->setMaxResults($query->limit);
        $q->setFirstResult(($query->page - 1) * $query->limit);
        $requests = $q->getResult('IndexByHydrator');

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW DeliveryBundle\Bus\Request\Query\DTO\RequestItem(
                    odi.id,
                    odi.orderDeliveryId,
                    bp.name,
                    oi.quantity
                )
            FROM DeliveryBundle:OrderDeliveryItem AS odi
            JOIN OrderBundle:OrderItem AS oi WITH oi.id = odi.orderItemId
            JOIN ContentBundle:BaseProduct AS bp WITH bp.id = oi.baseProductId
            WHERE odi.orderDeliveryId IN (:requestsIds)
        ");
        $q->setParameter('requestsIds', array_keys($requests));
        $items = $q->getArrayResult();

        foreach ($items as $item) {
            $requests[$item->deliveryRequestId]->items[] = $item;
        }

        return new DTO\RequestsList(array_values($requests), $total);
    }
}
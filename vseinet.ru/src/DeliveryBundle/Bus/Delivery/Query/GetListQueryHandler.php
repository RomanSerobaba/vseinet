<?php 

namespace DeliveryBundle\Bus\Delivery\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $clause = '';
        
        if ($query->number) {
            $clause .= " AND d.number = :number";
        }
        if ($query->status) {
            $clause .= " AND d.statusCode = :statusCode";
        }
        if ($query->shippedFrom) {            
            $clause .= " AND gr.completedAt >= :shippedFrom";
        }
        if ($query->shippedTo) {
            $clause .= " AND gr.completedAt <= :shippedTo";
        }
        if ($query->completedFrom) {
            $clause .= " AND d.completedAt >= :shippedFrom";
        }
        if ($query->completedTo) {
            $clause .= " AND d.completedAt <= :shippedTo";
        }
        if ($query->type) {
            $clause .= " AND d.type = :type";
        }
        if ($query->transportCompanyId) {
            $clause .= " AND d.transportCompanyId = :transportCompanyId";
        }
        
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                COUNT(DISTINCT d.dId)
            FROM DeliveryBundle:DeliveryDoc AS d
            LEFT JOIN DeliveryBundle:DeliveryItem AS i WITH i.deliveryId = d.number
            LEFT JOIN ReservesBundle:GoodsReleaseDoc AS gr WITH gr.parentDocumentId = d.dId
            WHERE 1 = 1{$clause}
        ");
        if ($query->number) {
            $q->setParameter('number', $query->number);
        }
        if ($query->status) {
            $q->setParameter('statusCode', $query->status);
        }
        if ($query->shippedFrom) {            
            $q->setParameter('shippedFrom', $query->shippedFrom);
        }
        if ($query->shippedTo) {            
            $q->setParameter('shippedTo', $query->shippedTo);
        }
        if ($query->completedFrom) {            
            $q->setParameter('completedFrom', $query->completedFrom);
        }
        if ($query->completedTo) {            
            $q->setParameter('completedTo', $query->completedTo);
        }
        if ($query->type) {            
            $q->setParameter('type', $query->type);
        }
        if ($query->transportCompanyId) {            
            $q->setParameter('transportCompanyId', $query->transportCompanyId);
        }
        $total = $q->getSingleScalarResult();

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW DeliveryBundle\Bus\Delivery\Query\DTO\Delivery(
                    d.dId,
                    d.number,
                    d.geoPointId,
                    d.courierId,
                    d.transportCompanyId,
                    d.title,
                    d.type,
                    d.statusCode, 
                    d.date,
                    COUNT(i.id),
                    gr.completedAt,
                    d.completedAt
                )
            FROM DeliveryBundle:DeliveryDoc AS d
            LEFT JOIN DeliveryBundle:DeliveryItem AS i WITH i.deliveryId = d.number
            LEFT JOIN ReservesBundle:GoodsReleaseDoc AS gr WITH gr.parentDocumentId = d.dId
            WHERE 1 = 1{$clause}
            GROUP BY d.dId, gr.dId
        ");
        if ($query->number) {
            $q->setParameter('number', $query->number);
        }
        if ($query->status) {
            $q->setParameter('statusCode', $query->status);
        }
        if ($query->shippedFrom) {            
            $q->setParameter('shippedFrom', $query->shippedFrom);
        }
        if ($query->shippedTo) {            
            $q->setParameter('shippedTo', $query->shippedTo);
        }
        if ($query->completedFrom) {            
            $q->setParameter('completedFrom', $query->completedFrom);
        }
        if ($query->completedTo) {            
            $q->setParameter('completedTo', $query->completedTo);
        }
        if ($query->type) {            
            $q->setParameter('type', $query->type);
        }
        if ($query->transportCompanyId) {            
            $q->setParameter('transportCompanyId', $query->transportCompanyId);
        }
        $q->setMaxResults($query->limit);
        $q->setFirstResult(($query->page - 1) * $query->limit);

        return new DTO\Items($q->getArrayResult(), $total);
    }
}
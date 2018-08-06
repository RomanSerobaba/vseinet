<?php 

namespace DeliveryBundle\Bus\TransportCompany\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetListForFilterQueryHandler extends MessageHandler
{
    public function handle(GetListForFilterQuery $query)
    {
        $clause = '';
        
        if ($query->isActive) {
            $clause .= " AND tc.isActive = :isActive";
        }
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW DeliveryBundle\Bus\TransportCompany\Query\DTO\TransportCompanyForFilter(
                    tc.id,
                    tc.name
                )
            FROM DeliveryBundle:TransportCompany AS tc
            WHERE 1 = 1{$clause}
        ");
        if ($query->isActive) {
            $q->setParameter('isActive', $query->isActive);
        }

        return $q->getArrayResult();
    }
}
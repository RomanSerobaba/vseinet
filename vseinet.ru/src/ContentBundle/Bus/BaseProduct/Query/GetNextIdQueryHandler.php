<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetNextIdQueryHandler extends MessageHandler
{
    public function handle(GetNextIdQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT bp.id 
            FROM ContentBundle:BaseProduct bp 
            INNER JOIN ContentBundle:Task t WITH t.categoryId = bp.categoryId 
            LEFT OUTER JOIN ContentBundle:BaseProductEditLog bpel WITH bpel.baseProductId = bp.id 
            WHERE t.managerId = :managerId AND bpel.id IS NULL bp.supplierAvailabilityCode > :outOfStock
        ");
        $q->setParameter('managerId', $this->get('user.identity')->getUser()->getId());
        $q->setParameter('outOfStock', ProductAvailabilityCode::OUT_OF_STOCK);
        $id = $q->getOneOrNullResult();
        
        if (null === $id) {
            $q = $em->createQuery("
                SELECT bp.id 
                FROM ContentBundle:BaseProduct bp 
                LEFT OUTER JOIN ContentBundle:BaseProductEditLog bpel WITH bpel.baseProductId = bp.id 
                WHERE bpel.id IS NULL bp.supplierAvailabilityCode > :outOfStock
            ");
            $q->setParameter('outOfStock', ProductAvailabilityCode::OUT_OF_STOCK);
            $id = $q->getOneOrNullResult();
        }

        return $id;
    }
}
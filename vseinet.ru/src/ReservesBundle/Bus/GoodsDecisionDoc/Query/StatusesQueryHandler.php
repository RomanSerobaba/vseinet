<?php 

namespace ReservesBundle\Bus\GoodsDecisionDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class StatusesQueryHandler extends MessageHandler
{
    public function handle(StatusesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $queryText = "
            select
                i.id as id,
                i.status_code,
                i.name,
                i.active,
                array_to_json(i.available_new_status_code) as available_new_status_code,
                i.completing

            from goods_decision_doc_status i
            ";

        if (!empty($query->onlyActive)) {
            
            $queryText .= "
            where i.active";

        }
        
        $queryText .= "
            order by i.id";
        
        $items = $em->createNativeQuery($queryText, new DTORSM(\DocumentBundle\Prototipe\StatusesDTO::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        return $items;
        
    }

}

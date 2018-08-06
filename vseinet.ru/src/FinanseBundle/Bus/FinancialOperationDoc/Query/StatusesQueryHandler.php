<?php
namespace FinanseBundle\Bus\FinancialOperationDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\Prototipe\StatusesDTO;

class StatusesQueryHandler extends MessageHandler
{
    public function handle(StatusesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $queryList = "
            select
                i.id as id,
                i.status_code,
                i.name,
                i.active,
                array_to_json(i.available_new_status_code) as available_new_status_code,
                i.completing

            from financial_operation_doc_status i
            ";

        if (!empty($query->onlyActive)) {
            
            $queryList .= "
            where i.active";

        }
        
        $queryList .= "
            order by i.id";
        
        $items = $em->createNativeQuery($queryList, new DTORSM(StatusesDTO::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        return $items;
        
    }

}

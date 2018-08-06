<?php   
namespace ReservesBundle\Bus\GoodsIssueDocType\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsIssueDocType\Query\DTO\GoodsIssueDocType(
                    gidt.id,
                    gidt.isActive,
                    gidt.isInteractive,
                    gidt.name,
                    gidt.availableGoodsStates,
                    gidt.byGoods,
                    gidt.byClient,
                    gidt.bySupplier
                )
            FROM ReservesBundle:GoodsIssueDocType gidt
            WHERE
                ";
        
        $setParameters = [];
        
        if (!empty($query->onlyInteractive)) {
            
            $queryText .= "
                gidt.isInteractive = :isInteractive";
            
            $setParameters['isInteractive'] = true;
            
        }
        
        if (empty($query->withInActive)) {
            
            $queryText .= "
                gidt.isActive = :isActive";
            
            $setParameters['isActive'] = true;
            
        }
        
        if (empty($setParameters)) {
            $queryText .= "
                true";
        }
        
        $queryDB = $this->getDoctrine()->getManager()->createQuery($queryText);
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
            
        return $queryDB->getResult();

    }

}
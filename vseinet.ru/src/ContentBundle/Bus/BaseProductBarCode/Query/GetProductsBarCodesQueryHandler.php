<?php

namespace ContentBundle\Bus\BaseProductBarCode\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetProductsBarCodesQueryHandler extends MessageHandler
{
    public function handle(GetProductsBarCodesQuery $query)
    {
        $setParameters = [];
        
        $queryText = "
            SELECT 
                NEW ContentBundle\Bus\BaseProductBarCode\Query\DTO\ProductBarCode(
                    pb.id,
                    pb.barCode,
                    pb.barCodeType,
                    pb.baseProductId,
                    bp.name,
                    pb.goodsPalletId,
                    gp.title,
                    bp.isHidden
                )
            FROM ContentBundle:BaseProductBarCode pb
            LEFT JOIN ContentBundle:BaseProduct bp WITH bp.id = pb.baseProductId
            LEFT JOIN ReservesBundle:GoodsPallet gp WITH gp.id = pb.goodsPalletId";

        if ($query->barCode) {
            
            $queryText .= "
                WHERE pb.barCode = :barCode";
            
            $setParameters['barCode'] = $query->barCode;
        }
        
        $queryText .= "
            ORDER BY pb.id DESC";
            
        $queryDB = $this->getDoctrine()->getManager()->createQuery($queryText);
        
        // Пагинация
        
        if ($query->limit) {
            $queryDB->
                    setMaxResults($query->limit);
        }
        if ($query->page) {
            $queryDB->
                    setFirstResult($query->page * $query->limit);
        }
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
            
        return $queryDB->getArrayResult();
    }

}
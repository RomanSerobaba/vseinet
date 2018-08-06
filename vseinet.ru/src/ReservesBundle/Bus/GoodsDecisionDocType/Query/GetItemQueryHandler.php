<?php 

namespace ReservesBundle\Bus\GoodsDecisionDocType\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {

        $queryText = "
            select
                i.id,
                i.is_active,
                i.name,
                i.by_goods,
                i.by_client,
                i.by_supplier,
                i.need_geo_room_id,
                i.need_base_product_id,
                i.need_price,
                i.need_money_back
                 
            from goods_decision_doc_type i
                       
            where i.id = {$query->id}
            ";

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, DTO\GoodsDecisionDocTypeItem::getRSM())
                ->getResult();
        
        if (empty($result)) throw new NotFoundHttpException('Тип претензии не найден');
        
        return $result[0];
    }

}
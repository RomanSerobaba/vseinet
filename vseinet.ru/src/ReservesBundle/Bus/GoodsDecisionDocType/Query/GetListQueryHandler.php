<?php 
namespace ReservesBundle\Bus\GoodsDecisionDocType\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {

        $em = $this->getDoctrine()->getManager();
        
        $setParameters = [];

        if (empty($query->goodsIssueDocId)) {
            
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
                ";

            if (empty($query->withInActive)) {

                $queryText .= "
                    where 
                    i.is_active = :isActive";

                $setParameters['isActive'] = true;

            }
            
            $queryDB = $this->getDoctrine()->getManager()
                    ->createNativeQuery($queryText, DTO\GoodsDecisionDocTypeItem::getRSM());

        }else{

            $queryText = "
                select 
                    case when q.quantity_goods = 0 then false else true end as by_goods,
                    case when q.quantity_client = 0 then false else true end as by_client,
                    case when q.quantity_supplier = 0 then false else true end as by_supplier
                from (
                    select

                        sum(i.delta_goods) as quantity_goods,
                        sum(i.delta_client) as quantity_client,
                        sum(i.delta_supplier) as quantity_supplier

                    from goods_issue_register i
                    where
                        i.goods_issue_doc_did = {$query->goodsIssueDocId}
                ) as q
                ";

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult("by_goods", "byGoods", "boolean");
            $rsm->addScalarResult("by_client", "byClient", "boolean");
            $rsm->addScalarResult("by_supplier", "bySupplier", "boolean");

            $need = $em->createNativeQuery($queryText, $rsm)->getSingleResult();

            $gcs =  "jsonb_build_object(
                'g', ". ($need['byGoods'] ? "true" : "false") .",
                'c', ". ($need['byClient'] ? "true" : "false") .",
                's', ". ($need['bySupplier'] ? "true" : "false") .")";
            
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
                where
                    jsonb_build_object(
                        'g', i.by_goods,
                        'c', i.by_client,
                        's', i.by_supplier) 
                    @>
                    {$gcs}
                ";

            if (empty($query->withInActive)) {

                $queryText .= "
                    and i.is_active = :isActive";

                $setParameters['isActive'] = true;

            }

            $queryDB = $this->getDoctrine()->getManager()
                    ->createNativeQuery($queryText, DTO\GoodsDecisionDocTypeItem::getRSM());

        }
                
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
            
        return $queryDB->getResult();

    }

}

<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {
        $queryText = "
            select
            
                i.did as id,
                case when i.parent_doc_did is null then
                    null
                else
                    json_build_object(
                        'id', parent_ad.did, 
                        'title', parent_ad.title, 
                        'document_type', pg_class.relname)
                end as parent_doc,
                i.status_code,
                i.goods_release_type,
                i.number,
                i.title,
                i.created_at,
                i.created_by,
                concat(
                    case when pc.firstname is null  then '' else concat(pc.firstname, ' ') end,
                    case when pc.secondname is null then '' else concat(pc.secondname, ' ') end,
                    case when pc.lastname is null   then '' else pc.lastname end) as created_name,
                i.completed_at,
                i.completed_by,
                concat(
                    case when pa.firstname is null  then '' else concat(pa.firstname, ' ') end,
                    case when pa.secondname is null then '' else concat(pa.secondname, ' ') end,
                    case when pa.lastname is null   then '' else pa.lastname end) as completed_name,
                i.registered_at,
                i.registered_by,
                concat(
                    case when pr.firstname is null  then '' else concat(pr.firstname, ' ') end,
                    case when pr.secondname is null then '' else concat(pr.secondname, ' ') end,
                    case when pr.lastname is null   then '' else pr.lastname end) as registered_name,
                i.geo_room_id,
                concat(
                    case when gc.name is null then '' else concat(gc.name, ',') end,
                    case when gp.name is null then '' else concat(gp.name, ',') end,
                    case when gr.name is null then '' else gr.name end) as geo_room_name,
                i.destination_room_id,
                concat(
                    case when dc.name is null then '' else concat(dc.name, ',') end,
                    case when dp.name is null then '' else concat(dp.name, ',') end,
                    case when dr.name is null then '' else dr.name end) as destination_room_name,
                i.is_waiting
  
            from goods_release_doc i
            
            -- документ-основние
            left join any_doc* parent_ad on parent_ad.did = i.parent_doc_did
            left join pg_class pg_class on pg_class.oid = parent_ad.tableoid

            -- именование автора
            left join \"user\" uc on i.created_by = uc.id  
            left join person pc on uc.person_id = pc.id  
            
            -- именование завершившего
            left join \"user\" ua on i.completed_by = ua.id  
            left join person pa on ua.person_id = pa.id  
            
            -- именование зарегистрировавшего
            left join \"user\" ur on i.registered_by = ur.id  
            left join person pr on ur.person_id = pr.id  
            
            -- именование склада-источника
            left join geo_room gr on gr.id = i.geo_room_id
            left join geo_point gp on gp.id = gr.geo_point_id
            left join geo_city gc on gc.id = gp.geo_city_id
            
            -- именование склада-приемника
            left join geo_room dr on dr.id = i.destination_room_id
            left join geo_point dp on dp.id = dr.geo_point_id
            left join geo_city dc on dc.id = dp.geo_city_id
            
            where i.did = {$query->id}";

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText,
                        new DTORSM(DTO\DocumentItem::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        if (empty($result)) throw new NotFoundHttpException('Документ не найден');

        return $result[0];

    }

}

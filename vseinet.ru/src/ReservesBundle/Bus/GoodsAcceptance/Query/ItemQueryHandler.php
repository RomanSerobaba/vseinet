<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $queryText = "
            select
            
                -- общие поля дкументов
                
                i.did as id,
                i.number,
                case when i.parent_doc_did is null then
                    null
                else
                    json_build_object(
                        'id', parent_ad.did, 
                        'title', parent_ad.title, 
                        'document_type', pg_class.relname)
                end as parent_doc,
                i.created_at as created_at,
                i.created_by as created_by,
                concat(
                    case when pc.firstname is null  then '' else concat(pc.firstname,
                        case when pc.lastname is null and pc.secondname is null then '' else ' ' end) end,
                    case when pc.secondname is null then '' else concat(pc.secondname,
                        case when pc.lastname is null   then '' else ' ' end) end,
                    case when pc.lastname is null   then '' else pc.lastname end) as created_name,
                i.completed_at as completed_at,
                i.completed_by as completed_by,
                concat(
                    case when pa.firstname is null  then '' else concat(pa.firstname,
                        case when pa.lastname is null and pa.secondname is null then '' else ' ' end) end,
                    case when pa.secondname is null then '' else concat(pa.secondname,
                        case when pa.lastname is null   then '' else ' ' end) end,
                    case when pa.lastname is null   then '' else pa.lastname end) as completed_name,
                i.registered_at as registered_at,
                i.registered_by as registered_by,
                concat(
                    case when pr.firstname is null  then '' else concat(pr.firstname,
                        case when pr.lastname is null and pr.secondname is null then '' else ' ' end) end,
                    case when pr.secondname is null then '' else concat(pr.secondname,
                        case when pr.lastname is null   then '' else ' ' end) end,
                    case when pr.lastname is null   then '' else pr.lastname end) as registered_name,
                i.title,
                i.status_code as status_code,
                
                -- персональные поля документа
                
                i.geo_room_id,
                concat(geo_c.name, ', ', geo_p.name, ', ', geo_r.name) as geo_room_name,
                i.geo_room_source as geo_room_source_id,
                case when i.geo_room_source is null then null
                else concat(geo_cs.name, ', ', geo_ps.name, ', ', geo_rs.name) end as geo_room_source_name,
                array_to_json(array(
                    select
                        json_build_object(
                            'id', ad_in.did,
                            'title', ad_in.title,
                            'document_type', pg_in.relname)
                    from any_doc* ad_in
                    left join pg_class pg_in on pg_in.oid = ad_in.tableoid
                    where to_jsonb(ad_in.did) <@ i.supplyies_documents_ids
                )) as supplyies_documents
                    
            from goods_acceptance_doc i
                
            -- Наименование склада
            left join geo_room geo_r on geo_r.id = i.geo_room_id
            left join geo_point geo_p on geo_p.id = geo_r.geo_point_id
            left join geo_city geo_c on geo_c.id = geo_p.geo_city_id
            
            -- Наименование склада-париёмника
            left join geo_room geo_rs on geo_rs.id = i.geo_room_source
            left join geo_point geo_ps on geo_ps.id = geo_rs.geo_point_id
            left join geo_city geo_cs on geo_cs.id = geo_ps.geo_city_id
            
            -- документ-основние
            left join any_doc* parent_ad on parent_ad.did = i.parent_doc_did
            left join pg_class pg_class on pg_class.oid = parent_ad.tableoid
            
            -- автор документа
            left join \"user\" uc on i.created_by = uc.id
            left join \"person\" pc on uc.person_id = pc.id
            
            -- кто закрыл документ
            left join \"user\" ua on i.completed_by = ua.id
            left join \"person\" pa on ua.person_id = pa.id

            -- кто провёл документ
            left join \"user\" ur on i.registered_by = ur.id
            left join \"person\" pr on ur.person_id = pr.id

            where i.did = {$query->id} 
        ";

        $document = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, 
                        new DTORSM(DTO\Document::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        if (empty($document)) throw new NotFoundHttpException('Документ не найден');
        
        return $document[0];
    }

}

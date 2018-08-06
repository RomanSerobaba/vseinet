<?php 

namespace SuppliersBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class ListForGoodsAcceptanceQuerryHandler extends MessageHandler
{
    public function handle(ListForGoodsAcceptanceQuerry $query):array
    {
        
        $items = $this->getDoctrine()->getManager()->createNativeQuery("
            with
              source_data as(
                select
                  sd.supplier_id as id,
                  su.code,
                  su.name,
                  sd.did,
                  sd.title
                from supply_doc sd
                left join goods_acceptance_doc ga on to_jsonb(sd.did) <@ ga.supplyies_documents_ids
                left join supplier su on su.id = sd.supplier_id
                where
                  ga.did is null and
                  sd.completed_at is not null and
                  sd.destination_room_id = {$query->geoRoomId}
                  ),

              group_data as(
                select distinct
                  id,
                  code,
                  name
                from source_data)

              select
                gd.id,
                gd.code,
                gd.name,
                to_json(array(
                  select 
                    sd.did
            --        json_build_object(
            --          'id', sd.did,
            --          'title', sd.title)
                  from source_data sd
                  where
                    sd.id = gd.id
                )) as supplies_documents_ids
              from group_data gd
        ", new DTORSM(DTO\ListForGoodsAcceptanceDTO::class))
                ->getResult('DTOHydrator');

        return $items;
    }
}
<?php

namespace ReservesBundle\Bus\InventoryProductCounter\Command;

use AppBundle\Bus\Message\MessageHandler;
//use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\InventoryProductCounter;
use ReservesBundle\Entity\InventoryProduct;
use ReservesBundle\Entity\Inventory;

class SetCommandHandler extends MessageHandler
{
    public function handle(SetCommand $command) 
    {
        $command->updateAt = new \DateTime;
                
        $em = $this->getDoctrine()->getManager();
        
        // Проверка статуса документа

        $inventory = $em->getRepository(Inventory::class)->find($command->inventoryId);
        if (Inventory::INVENTORY_STATUS_STARTED != $inventory->getStatus()) {
            throw new ConflictHttpException('При данном статусе инвентаризации внесение остатков не возможно.');
        }

        $em->getConnection()->beginTransaction();
        try {
            
            // Увеличение количества подсчитанного товара
            
            $item = $em->getRepository(InventoryProductCounter::class)->findOneBy([
                'inventoryDId' => $command->inventoryId, 
                'participantId' => $this->get('user.identity')->getUser()->getId(),
                'baseProductId' => $command->id
            ]);
            
            if ($item instanceof InventoryProductCounter) {
                $item->setFoundQuantity($command->foundQuantity);
                $item->setUpdateAt(new \DateTime);
            }else{
                $item = new InventoryProductCounter();
                $item->setBaseProductId($command->id);
                $item->setInventoryDId($command->inventoryId);
                $item->setParticipantId($this->get('user.identity')->getUser()->getId());
                $item->setFoundQuantity($command->foundQuantity);
                $item->setUpdateAt(new \DateTime);
            }

            $em->persist($item);
            $em->flush();
            
            
            // Обновление количества товара по учету
            
            $inventoryProducts = $em->getRepository(InventoryProduct::class)->findBy([
                'inventoryDId' => $command->inventoryId, 
                'baseProductId' => $command->id
            ]);
        
            foreach ($inventoryProducts as $inventoryProduct) {
                $em->remove($inventoryProduct);
            }
            $em->flush();                

            $queryText = "

                insert into inventory_product (inventory_did, base_product_id, purchase_price, retail_price, initial_quantity) (

                    with all_wares as (
                        select
                            ii.did,
                            rl.base_product_id,
                            ii.geo_room_id,
                            sum(rl.delta * si.purchase_price) as in_sum,
                            sum(rl.delta) as sum_delta
                        from goods_reserve_register rl
                        left join inventory ii
                            on ii.did = :inventoryDId
                        left join base_product bp
                            on bp.id = rl.base_product_id
                        left join supply_item si on
                            si.id = rl.supply_item_id
                        where
                            rl.geo_room_id = ii.geo_room_id and
                            rl.base_product_id = :baseProductId and
                            rl.registered_at <= :updateAt
                        group by
                            ii.did,
                            rl.base_product_id,
                            ii.geo_room_id
                        having sum(rl.delta) <> 0
                    )

                    select
                        aw.did,
                        aw.base_product_id,
                        case when aw.in_sum is null
                            then 0
                            else aw.in_sum/aw.sum_delta end as purchase_price,
                        case when pp.price is null
                            then
                            case when pp_global.price is null
                                then 0
                                else pp_global.price end
                            else pp.price end as retail_price,
                        aw.sum_delta
                    from all_wares aw
                    left join geo_point gp
                        on gp.id = aw.geo_room_id
                    left join product pp
                        on pp.base_product_id = aw.base_product_id
                        and pp.geo_city_id is null
                    left join product pp_global
                        on pp_global.base_product_id = aw.base_product_id
                        and pp_global.geo_city_id = gp.geo_city_id
                )
                ";
            
            $rsm = new ResultSetMapping();
            $queryDB = $this->getDoctrine()->getManager()->
                    createNativeQuery($queryText, $rsm)->
                    setParameters([
                        'inventoryDId' => $command->inventoryId,
                        'baseProductId' => $command->id,
                        'updateAt' => $command->updateAt
                    ]);

            $queryDB->execute();
            $em->flush();
            
            $em->getConnection()->commit();
            
        } catch (Exception $ex) {

            $em->getConnection()->rollback();
            
            throw $e;
            
        }
    }
}
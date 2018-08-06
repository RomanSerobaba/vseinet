<?php

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use RegisterBundle\Entity\GoodsReserveRegister;
use ReservesBundle\Entity\Inventory;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;

class RegistrationCommandHandler extends MessageHandler
{
    
    public function handle(RegistrationCommand $command) 
    {
        $currentUser = $this->get('user.identity')->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(Inventory::class)->find($command->id);
        
        if (!$document instanceof Inventory) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ уже проведён (команда)');
        }

        if (Inventory::INVENTORY_STATUS_COMPLETED != $document->getStatus()) {
            return;
        }
        
        // Проверка готовности корректно заполненного документа к проведению
        
        // Записываем новые движения документа
        
        // Удаляем старые записи из движений товаров

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from goods_reserve_register
            where
                registrator_type_code = 'inventory'::document_type_code and
                registrator_id = :inventoryDId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['inventoryDId' => $command->id]);

        $queryDB->execute();


        // Удаляем старые записи из партий

        $queryText = "
            delete from supply_item
            where
                parent_doc_id = :inventoryDId and
                parent_doc_type = 'inventory'::document_type_code
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['inventoryDId' => $command->id]);

        $queryDB->execute();


        // Получаем список конфликтных строк с учетом заказов, для дальнейшего распределения полученных резервов по заказам.

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('actual_data',      'actualData',      'datetime');
        $rsm->addScalarResult('geo_room_id',      'geoRoomId',       'integer');
        $rsm->addScalarResult('inventory_did',    'inventoryId',     'integer');
        $rsm->addScalarResult('base_product_id',  'baseProductId',   'integer');
        $rsm->addScalarResult('supply_item_id',   'supplyItemId',    'integer');
        $rsm->addScalarResult('reserve_type',     'reserveType',     'string');
        $rsm->addScalarResult('initial_quantity', 'initialQuantity', 'integer');
        $rsm->addScalarResult('found_quantity',   'foundQuantity',   'integer');
        $rsm->addScalarResult('order_item_id',    'orderItemId',     'integer');
        $rsm->addScalarResult('goods_request_id', 'goodsRequestId',  'integer');
        $rsm->addScalarResult('need_quantity',    'needQuantity',    'integer');
        $rsm->addScalarResult('order',            'Order',           'integer');

        $queryText = "

            -- Выбираем конфликтные строки с разбивкой по заказам и заявкам

            select
                iss.in_price_date as actual_data,
                iss.geo_room_id,
                iss.inventory_did,
                iss.base_product_id,
                grr.supply_item_id,
                grr.goods_condition_code,
                iss.initial_quantity,
                iss.found_quantity,
                grr.order_item_id,
                grr.goods_request_id,
                sum(grr.delta) as need_quantity,
                case
                    when grr.order_item_id is not null
                    then 0
                    else 
                        case
                            when grr.goods_request_id is not null
                            then 1
                            else 9
                        end
                end as order
            from (
                -- связываем таблицы, подставляем заказы и заявки
                select
                    case when ipc.update_at is null
                        then ii.created_at
                        else ipc.update_at end as in_price_date,
                    ii.geo_room_id as geo_room_id,
                    ii.did as inventory_did,
                    case when ip.base_product_id is null
                        then ipc.base_product_id
                        else ip.base_product_id end as base_product_id,
                    case when ip.initial_quantity is null
                        then 0
                        else ip.initial_quantity end as initial_quantity,
                    case when ipc.found_quantity is null
                        then 0
                        else ipc.found_quantity end as found_quantity
                from inventory ii
                left join inventory_product ip on
                    ip.inventory_did = ii.did
                left join inventory_product_counter ipc on
                    ipc.inventory_did = ip.inventory_did and
                    ipc.base_product_id = ip.base_product_id
                where
                    ii.did = :inventoryDId
                ) iss
            left join goods_reserve_register grr on
                grr.base_product_id = iss.base_product_id and
                grr.geo_room_id     = iss.geo_room_id and
                grr.registered_at  <= iss.in_price_date
            where
                iss.found_quantity <> iss.initial_quantity and
                grr.goods_condition_code in ('free'::goods_condition_code, 'reserved'::goods_condition_code)
            group by
                iss.in_price_date,
                iss.geo_room_id,
                iss.inventory_did,
                iss.base_product_id,
                grr.supply_item_id,
                grr.goods_condition_code,
                iss.initial_quantity,
                iss.found_quantity,
                grr.order_item_id,
                grr.goods_request_id,
                case
                    when grr.order_item_id is not null
                    then 0
                    else 
                        case
                            when grr.goods_request_id is not null
                            then 1
                            else 9
                        end
                end
            order by
                iss.base_product_id,
                case
                    when grr.order_item_id is not null
                    then 0
                    else 
                        case
                            when grr.goods_request_id is not null
                            then 1
                            else 9
                        end
                end,
                grr.order_item_id,
                grr.goods_request_id

        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['inventoryDId' => $command->id]);

        $allConflict = $queryDB->getArrayResult();
        $currentBaseProductId = -1;
        $currentFoundQuantity = 0;
        foreach ($allConflict as $currentConflict) {

            if ($currentBaseProductId != $currentConflict['baseProductId']) {

                if (0 != $currentFoundQuantity) {

                    // Приходуем излишки

                    $grr = new GoodsReserveRegister();

                    $grr->setRegisteredAt($inventory->getCompletedAt());
                    $grr->setCreatedAt(new \DateTime);
                    $grr->setCreatedBy($currentUser->getId());
                    $grr->setRegisterOperationTypeCode(OperationTypeCode::INVENTORY);
                    $grr->setRegistratorTypeCode(DocumentTypeCode::INVENTORY);
                    $grr->setRegistratorId($inventory->getId());

                    $grr->setBaseProductId($currentBaseProductId);
                    $grr->setSupplyItemId($this->addMySupply($em, $inventory->getId(), $inventory->getCompletedAt(), $currentBaseProductId, $currentFoundQuantity));
                    $grr->setGeoRoomId($inventory->getGeoRoomId());
                    $grr->setGoodsConditionCode(GoodsConditionCode::FREE);

                    $grr->setDelta($currentFoundQuantity);

                    $em->persist($grr);
                    $em->flush();
                }

                $currentBaseProductId = $currentConflict['baseProductId'];
                $currentFoundQuantity = $currentConflict['foundQuantity'];

            }

            if ($currentFoundQuantity > $currentConflict['needQuantity']) {

                $currentFoundQuantity -= $currentConflict['needQuantity'];
                continue; // Недостачи нет идём дальше

            }

            // Списываем недостачу

            $removeQuantity = $currentConflict['needQuantity'] - $currentFoundQuantity;

            $grr = new GoodsReserveRegister();

            $grr->setRegisteredAt($inventory->getCompletedAt());
            $grr->setCreatedAt(new \DateTime);
            $grr->setCreatedBy($currentUser->getId());
            $grr->setRegisterOperationTypeCode(OperationTypeCode::INVENTORY);
            $grr->setRegistratorTypeCode(DocumentTypeCode::INVENTORY);
            $grr->setRegistratorId($inventory->getId());

            $grr->setBaseProductId($currentBaseProductId);
            $grr->setSupplyItemId($currentConflict['supplyItemId']);
            $grr->setGeoRoomId($inventory->getGeoRoomId());
            $grr->setGoodsConditionCode($currentConflict['goods_condition_code']);
            $grr->setOrderItemId($currentConflict['orderItemId']);
            $grr->setGoodsRequestId($currentConflict['goodsRequestId']);

            $grr->setDelta(-$removeQuantity);

            $em->persist($grr);
            $em->flush();

            $currentFoundQuantity -= $removeQuantity;

        }

        if (0 != $currentFoundQuantity) {

            //Если остался какой-то остаток - оформляем излишки

            if (0 != $currentFoundQuantity) {

                // Приходуем излишки

                $grr = new GoodsReserveRegister();

                $grr->setRegisteredAt($inventory->getCompletedAt());
                $grr->setCreatedAt(new \DateTime);
                $grr->setCreatedBy($currentUser->getId());
                $grr->setRegisterOperationTypeCode(OperationTypeCode::INVENTORY);
                $grr->setRegistratorTypeCode(DocumentTypeCode::INVENTORY);
                $grr->setRegistratorId($inventory->getId());

                $grr->setBaseProductId($currentBaseProductId);
                $grr->setSupplyItemId($this->addMySupply($em, $inventory->getId(), $inventory->getCompletedAt(), $currentBaseProductId, $currentFoundQuantity));
                $grr->setGeoRoomId($inventory->getGeoRoomId());
                $grr->setGoodsConditionCode(GoodsConditionCode::FREE);

                $grr->setDelta($currentFoundQuantity);

                $em->persist($grr);
                $em->flush();
            }

        }

        // Отметка об удачной записи
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();
        
    }
    
    /**
     * Добавить запись для оформления излишка и получить её идентификатор
     * 
     * @param ReservesBundle\Entity\Inventory $inventory
     * @param \DateTime                       $actualData
     * @param int                             $baseProductId
     * @param int                             $quantity
     * 
     * @return int
     */
    protected function addMySupply($em, $inventoryDId, $actualData, $baseProductId, $quantity)
    {
 
        $SupplyItem = new SupplyItem();
        $SupplyItem->setBaseProductId($baseProductId);
        $SupplyItem->setInventoryDId($inventoryDId);
        $SupplyItem->setPurchasePrice($this->getSupplierPrice($em, $actualData, $baseProductId));
        $SupplyItem->setQuantity($quantity);
        
        //$em = $this->getDoctrine()->getManager();
        $em->persist($SupplyItem);
        $em->flush();
        
        return $SupplyItem->getId();
    }
            
    /**
     * Получить предпологаемую цену поставщика для излишка
     * 
     * @param \DateTime $actualData
     * @param int       $baseProductId
     * 
     * @return int
     */
    protected function getSupplierPrice($em, $actualData, $baseProductId)
    {

        $queryText = "
            select
                case when 
                    bp.supplier_price is not null and 
                    bp.supplier_price > 1
                then bp.supplier_price
                else
                    case when 
                        sp.purchase_price is not null and 
                        sp.purchase_price > 1
                    then sp.purchase_price
                    else 1
                    end
                end as suppliuer_price
            from base_product bp, (
                select
                    si.purchase_price
                from supply_item si
                left join supply so on
                    so.id = si.supply_id
                left join shipment sh on
                    sh.id = so.shipment_id
                left join goods_issue gi on
                    gi.id = si.goods_issue_id
--                left join goods_packaging gp on
--                    gp.id = si.goods_packaging_id
                left join inventory ii on
                    ii.did = si.inventory_did
                where
--                    (gi.goods_decision_at <= :actualData or gi.goods_decision_at is null) and
                    (gi.created_at <= :actualData or gi.created_at is null) and
--                    (gp.approved_at <= :actualData or gp.approved_at is null) and
                    (ii.completed_at <= :actualData or ii.completed_at is null) and
                    (sh.accepted_at <= :actualData or sh.accepted_at is null) and
                    si.purchase_price > 1 and
                    si.base_product_id = :baseProductId
                order by 
--                    case when gi.goods_decision_at is not null
--                    then gi.goods_decision_at
                    case when gi.created_at is not null
                    then gi.created_at
                    else
--                        case when gp.approved_at is not null
--                        then gp.approved_at
--                        else
                            case when ii.completed_at is not null
                            then ii.completed_at
                            else
                                case when sh.accepted_at is not null
                                then sh.accepted_at
                                else null
                                end
                            end
--                        end
                    end DESC
                limit 1
            ) sp
            where
                bp.id = :baseProductId
        ";
        
        $queryText = "
            select
                case when 
                    bp.supplier_price is not null and 
                    bp.supplier_price > 1
                then bp.supplier_price
                else
                    case when 
                        sp.purchase_price is not null and 
                        sp.purchase_price > 1
                    then sp.purchase_price
                    else 1
                    end
                end as suppliuer_price
            from base_product bp, (
                select
                    si.purchase_price
                from supply_item si
                left join supply so on
                    so.id = si.supply_id
                left join shipment sh on
                    sh.id = so.shipment_id
                left join goods_issue gi on
                    gi.id = si.goods_issue_id
                left join inventory ii on
                    ii.did = si.inventory_did
                where
                    (gi.created_at <= :actualData or gi.created_at is null) and
                    (ii.completed_at <= :actualData or ii.completed_at is null) and
                    (sh.accepted_at <= :actualData or sh.accepted_at is null) and
                    si.purchase_price > 1 and
                    si.base_product_id = :baseProductId
                order by 
                    case when gi.created_at is not null
                    then gi.created_at
                    else
                            case when ii.completed_at is not null
                            then ii.completed_at
                            else
                                case when sh.accepted_at is not null
                                then sh.accepted_at
                                else null
                                end
                            end
                    end DESC
                limit 1
            ) sp
            where
                bp.id = :baseProductId
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('suppliuer_price', 'supplierPrice', 'integer');
        
        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters([
                    'baseProductId' => $baseProductId,
                    'actualData' => $actualData
                ]);

        $allPrices = $queryDB->getArrayResult();
        
        $supplierPrice = 0;
        foreach ($allPrices as $price) {
            $supplierPrice = $price['supplierPrice'];
        }
        
        return $supplierPrice;
        
    }
}

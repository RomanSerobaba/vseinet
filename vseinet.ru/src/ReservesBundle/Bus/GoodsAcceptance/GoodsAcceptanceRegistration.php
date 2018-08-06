<?php
/*
 * Автор: Денис О. Конашёнок
 */

namespace ReservesBundle\Bus\GoodsAcceptance;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\GoodsAcceptanceType;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Entity\GoodsAcceptanceItem;
use RegisterBundle\Entity\GoodsReserveRegister;
use SupplyBundle\Entity\SupplyItem;

class GoodsAcceptanceRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \ReservesBundle\Entity\GoodsAcceptance $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager            $em          менеджер сущностей
     * @param \AppBundle\Entity\User                 $currentUser пользователь, регистрирующий документ
     */
    public static function Registration(GoodsAcceptance $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        if (!empty($document->getRegisteredAt()))
            throw new ConflictHttpException('Документ уже проведён');

        if (empty($document->getCompletedAt()))
            return;

        //////////////////////////////////////////////////////
        //
        //  Проверка, весь-ли товар обработан
        //  Посмотрим на разницу initialQuantity и quantity
        //

        $queryText = "

            select
                sum(i.initialQuantity - (case when i.quantity is null then 0 else i.quantity end)) as delta
            from ReservesBundle\Entity\GoodsAcceptanceItem as i
            where
                i.goodsAcceptanceDId = :goodsAcceptanceDId
        ";

        $queryDB = $em->createQuery($queryText)
                ->setParameters([
                    'goodsAcceptanceDId' => $document->getDId(),
                ]);

        $results = $queryDB->getArrayResult();

        if (!empty($results[0]['delta'])) {
            if ($results[0]['delta'] > 0) {
                throw new ConflictHttpException('Не весь товар обработан. Количество не обработанных единиц товара: '. $results[0]['delta']);
            }
            if ($results[0]['delta'] < 0) {
                throw new ConflictHttpException('Обработанно товара больше, чем затребованно. Количество излишне обработанных единиц товара: '. abs($results[0]['delta']));
            }
        }

        //////////////////////////////////////////////////////
        //
        //  Ргистрация
        //

        $now = new \DateTime();
        $nowText = $now->format('Y-m-d H:i:s');
        $actualDate = $document->getCompletedAt();
        $actualDateText = $actualDate->format('Y-m-d H:i:s');
        $geoRoomId = $document->getGeoRoomId();
        $geoRoomSourceId = empty($document->getGeoRoomSource()) ? 'null' : $document->getGeoRoomSource();
        $documentId = $document->getDId();
        $currentUserId = $currentUser->getId();
        $documentTypeCode = \AppBundle\Enum\DocumentTypeCode::GOODS_ACCEPTANCE;

        // Обработка товаров с партиями
        
        $queryText = "

            with

                remove_items as(
                    select
                        odr.order_type_code,
                        gair.base_product_id,
                        gair.supply_item_id,
                        gair.order_item_id,
                        gair.goods_pallet_id,
                        sum(gair.quantity) as quantity
                    from
                        goods_acceptance_item gair
                    left join order_item oir on oir.id = gair.order_item_id
                    left join order_doc odr on odr.number = oir.order_id   -- к исправлению number->did
                    where
                        gair.goods_acceptance_did = {$documentId} and
                        gair.supply_item_id is not null
                    group by
                        odr.order_type_code,
                        gair.base_product_id,
                        gair.supply_item_id,
                        gair.order_item_id,
                        gair.goods_pallet_id
                ),

                accept_items as(
                    select
                        oda.order_type_code,
                        gaia.base_product_id,
                        gaia.supply_item_id,
                        gaia.order_item_id,
                        gaia.goods_pallet_id,
                        sum(gaia.quantity) as quantity
                    from
                        goods_acceptance_item gaia
                    left join order_item oia on oia.id = gaia.order_item_id
                    left join order_doc oda on oda.number = oia.order_id   -- к исправлению number->did
                    where
                        gaia.goods_acceptance_did = {$documentId} and
                        gaia.supply_item_id is not null and
                        gaia.goods_state_code = 'normal'::goods_state_code
                    group by
                        oda.order_type_code,
                        gaia.base_product_id,
                        gaia.supply_item_id,
                        gaia.order_item_id,
                        gaia.goods_pallet_id
                ),

                issue_items as(
                    select
                        ods.order_type_code,
                        gais.base_product_id,
                        gais.supply_item_id,
                        gais.order_item_id,
                        gais.goods_pallet_id,
                        sum(gais.quantity) as quantity
                    from
                        goods_acceptance_item gais
                    left join order_item ois on ois.id = gais.order_item_id
                    left join order_doc ods on ods.number = ois.order_id    -- к исправлению number->did
                    where
                        gais.goods_acceptance_did = {$documentId} and
                        gais.supply_item_id is not null and
                        gais.goods_state_code <> 'normal'::goods_state_code
                    group by
                        ods.order_type_code,
                        gais.base_product_id,
                        gais.supply_item_id,
                        gais.order_item_id,
                        gais.goods_pallet_id
                ),
                
                insert_remove_items as(
                insert into goods_reserve_register as gr0 (
                    registrator_type_code,
                    registrator_id,
                    registered_at,
                    created_at,
                    created_by,
                    register_operation_type_code,
                    base_product_id,
                    goods_condition_code,
                    supply_item_id,
                    order_item_id,
                    geo_room_id,
                    delta,
                    goods_pallet_id)
                (
                    select
                        '{$documentTypeCode}'::document_type_code,
                        {$documentId},
                        '{$actualDateText}',
                        '{$nowText}',
                        {$currentUserId},
                        'goods_acceptance'::operation_type_code,
                        ri.base_product_id,
                        case ri.order_type_code
                            when 'legal'::order_type_code then 'reserved'::goods_condition_code
                            when 'site'::order_type_code then 'reserved'::goods_condition_code
                            when 'shop'::order_type_code then 'reserved'::goods_condition_code
                            when 'equipment'::order_type_code then 'equipment'::goods_condition_code
                            when 'consumables'::order_type_code then 'reserved'::goods_condition_code
                            else 'free'::goods_condition_code end,
                        ri.supply_item_id,
                        ri.order_item_id,
                        {$geoRoomSourceId},
                        -ri.quantity,
                        ri.goods_pallet_id
                    from remove_items ri
                )
                returning
                    'remove'::text as status,
                    delta as quantity
                ),

                insert_accept_items as(
                insert into goods_reserve_register as gr1 (
                    registrator_type_code,
                    registrator_id,
                    registered_at,
                    created_at,
                    created_by,
                    register_operation_type_code,
                    base_product_id,
                    goods_condition_code,
                    supply_item_id,
                    order_item_id,
                    geo_room_id,
                    delta,
                    goods_pallet_id)
                (
                    select
                        '{$documentTypeCode}'::document_type_code,
                        {$documentId},
                        '{$actualDateText}',
                        '{$nowText}',
                        {$currentUserId},
                        'goods_acceptance'::operation_type_code,
                        ai.base_product_id,
                        case ai.order_type_code
                            when 'legal'::order_type_code then 'reserved'::goods_condition_code
                            when 'site'::order_type_code then 'reserved'::goods_condition_code
                            when 'request'::order_type_code then 'free'::goods_condition_code
                            when 'shop'::order_type_code then 'reserved'::goods_condition_code
                            when 'equipment'::order_type_code then 'equipment'::goods_condition_code
                            when 'consumables'::order_type_code then 'reserved'::goods_condition_code
                            when 'resupply'::order_type_code then 'free'::goods_condition_code
                            else 'free'::goods_condition_code
                        end,
                        ai.supply_item_id,
                        ai.order_item_id,
                        {$geoRoomId},
                        ai.quantity,
                        ai.goods_pallet_id
                    from accept_items ai
                )
                returning
                    'accept'::text as status,
                    delta as quantity
                ),

                insert_issue_items as(
                insert into goods_reserve_register as gr2 (
                    registrator_type_code,
                    registrator_id,
                    registered_at,
                    created_at,
                    created_by,
                    register_operation_type_code,
                    base_product_id,
                    goods_condition_code,
                    supply_item_id,
                    order_item_id,
                    geo_room_id,
                    delta,
                    goods_pallet_id)
                (
                    select
                        '{$documentTypeCode}'::document_type_code,
                        {$documentId},
                        '{$actualDateText}',
                        '{$nowText}',
                        {$currentUserId},
                        'goods_acceptance'::operation_type_code,
                        ii.base_product_id,
                        'issued'::goods_condition_code,
                        ii.supply_item_id,
                        ii.order_item_id,
                        {$geoRoomId},
                        ii.quantity,
                        ii.goods_pallet_id
                    from issue_items ii
                )
                returning
                    'issued'::text as status,
                    delta as quantity
                )
               
            select 
                i1.status,
                i1.quantity
            from insert_remove_items i1
            
            union all

            select 
                i2.status,
                i2.quantity
            from insert_accept_items i2
            
            union all

            select 
                i3.status,
                i3.quantity
            from insert_issue_items i3
            ";

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult("status", "status", "string");
            $rsm->addScalarResult("quantity", "quantity", "integer");
            
            $result = $em->createNativeQuery($queryText, $rsm)
                    ->getResult();
            
        // Обработка товаров без партий
            
        $items = $em->getRepository(GoodsAcceptanceItem::class)
                ->findBy([
                    'goodsAcceptanceDId' => $documentId,
                    'supplyItemId' => null]);
        if (empty($items))
            return;

        foreach ($items as $item) {
            
            $supplyItem = new SupplyItem();
            $supplyItem->setParentDocId($documentId);
            $supplyItem->setParentDocType($documentTypeCode);
            $supplyItem->setBaseProductId($item->getBaseProductId());
            $supplyItem->setQuantity($item->getQuantity());
            $supplyItem->setPurchasePrice(0);
            $em->persist($supplyItem);
            $em->flush($supplyItem);
            
            $reg = new GoodsReserveRegister();
            $reg->setRegistratorTypeCode($documentTypeCode);
            $reg->setRegistratorId($documentId);
            $reg->setRegisteredAt($actualDate);
            $reg->setCreatedAt($now);
            $reg->setCreatedBy($currentUserId);
            $reg->setRegisterOperationTypeCode('goods_acceptance');
            $reg->setBaseProductId($item->getBaseProductId());
            $reg->setGoodsConditionCode('normal' == $item->getGoodsStateCode() ? 'free' : 'issued');
            $reg->setSupplyItemId($supplyItem->getId());
            $reg->setGeoRoomId($geoRoomId);
            $reg->setDelta($item->getQuantity());
            $em->persist($reg);

        }

    }

}

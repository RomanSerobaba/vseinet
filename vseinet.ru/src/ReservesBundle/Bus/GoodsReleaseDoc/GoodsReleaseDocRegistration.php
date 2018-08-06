<?php
/*
 * Автор: Денис О. Конашёнок
 */

namespace ReservesBundle\Bus\GoodsReleaseDoc;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\GoodsReleaseType;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Entity\GoodsReleaseDocItem;
use RegisterBundle\Entity\GoodsReserveRegister;

class GoodsReleaseDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     * 
     * @param \ReservesBundle\Entity\GoodsReleaseDoc $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager            $em          менеджер сущностей
     * @param \AppBundle\Entity\User                 $currentUser пользователь, регистрирующий документ
     */
    public static function Registration(GoodsReleaseDoc $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ уже проведён');
        }

        if (empty($document->getCompletedAt())) {
            return;
        }

        //////////////////////////////////////////////////////
        //
        //  Ргистрация документа
        //
        
        $actualDate = $document->getCompletedAt();
                
        $rFound = new ResultSetMapping();
        $rFound->addScalarResult('base_product_id', 'baseProductId', 'integer');
        $rFound->addScalarResult('order_item_id', 'orderItemId', 'integer');
        $rFound->addScalarResult('supply_item_id', 'supplyItemId', 'integer');
        $rFound->addScalarResult('goods_pallet_id', 'goodsPalletId', 'integer');
        $rFound->addScalarResult('goods_state_code', 'goodsStateCode', 'integer');
        $rFound->addScalarResult('quantity', 'quantity', 'integer');
        
        $queryText = "            
            select
                gi.base_product_id,
                gi.order_item_id,
                gi.supply_item_id,
                gi.goods_pallet_id,
                gi.goods_state_code,
                sum(gi.quantity) as quantity
            from goods_release_item as gi
            where
                gi.goods_release_did = :goodsReleaseDId
            group by
                gi.base_product_id,
                gi.order_item_id,
                gi.supply_item_id,
                gi.goods_pallet_id,
                gi.goods_state_code
            ";

        
        $queryDB = $em->createNativeQuery($queryText, $rFound)
                ->setParameters([
                    'goodsReleaseDId' => $document->getDId(),
                ]);

        $results = $queryDB->getResult();

        $geoRoomId = $document->getGeoRoomId();
        
        $toGRR = [];
        $toIssue = [];
        $toWaitingRelease = [];

        foreach ($results as $value) {
            
            if ($value['goodsStateCode'] == \AppBundle\Enum\GoodsStateCode::NORMAL) {
                
                // нормальная отгрузка
                
                $toGRR[] = [
                    'operationTypeCode' => setRegisterOperationTypeCode(goodReleaseType2OperationTypeCode($document->getGoodsReleaseType())),
                    'baseProductId' => $value['baseProductId'],
                    'supplyItemId' => $value['supplyItemId'],
                    'orderItemId' => $value['orderItemId'],
                    'goodsStateCode' => $value['goodsStateCode'],
                    'goodsPalletId' => $value['goodsPalletId'],
                    'qyantity' => $value['qyantity']
                ];
                    
            }elseif ($value['goodsStateCode'] == \AppBundle\Enum\GoodsStateCode::IS_WAITING) {
                
                // отложенная отгрузка
                
                if (GoodsReleaseType::CLIENT != $document->getGoodsReleaseType())
                
                $toWaitingRelease[] = [
                    'baseProductId' => $value['baseProductId'],
                    'orderItemId' => $value['orderItemId'],
                    'supplyItemId' => $value['supplyItemId'],
                    'initialQuantity' => $value['quantity']
                ];
                
            }else{                                                                               
                
                // претензии
                
                $toIssue[] = [
                    'baseProductId' => $value['baseProductId'],
                    'orderItemId' => $value['orderItemId'],
                    'supplyItemId' => $value['supplyItemId'],
                    'goodsStateCode' => $value['goodsStateCode'],
                    'quantity' => $value['quantity']
                ];
                
            }

        }

        // Нормальная отгрузка. Пишем и проверяем регистр остатков
        
        foreach ($toGRR as $value) {
            
                $grr = new GoodsReserveRegister();

                $grr->setCreatedAt(new \DateTime);
                $grr->setCreatedBy($currentUser->getId());

                $grr->setRegistratorId($document->getDId());
                $grr->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_RELEASE); // Удалить при переходе всех документов на DId

                $grr->setRegisteredAt($actualDate);
                $grr->setRegisterOperationTypeCode($value['operationTypeCode']);

                $grr->setGeoRoomId($document->getGeoRoomId());
                $grr->setBaseProductId($value['baseProductId']);
                $grr->setSupplyItemId($value['supplyItemId']);
                $grr->setGoodsPalletId($value['goodsPalletId']);
                
                // Уточним состояние товара
            
                $goodsSQ = GoodsReleaseDocRegistration::checkQuantity(
                        $actualDate, 
                        $geoRoomId, 
                        $value['baseProductId'],
                        $value['supplyItemId'],
                        $value['quantity'],
                        $value['orderItemId'],
                        $value['goodsStateCode'],
                        $value['goodsPalletId']);
                
                $grr->setOrderItemId($goodsSQ['orderItemId']);
                $grr->setDelta(-$goodsSQ['qyantity']);
                $grr->setGoodsConditionCode($goodsSQ['goodsConditionCode']);                        

                $grr->setGoodsReleaseId($document->getDId());

                $em->persist($grr);

        }
        
        // если есть отложенные товары, то делаем отложенный документ отгрузки
        
        if (!empty($toWaitingRelease)) {
            
            $goodsReleaseIsWaiting = new GoodsReleaseDoc();

            $goodsReleaseIsWaiting->setGeoRoomId($document->getGeoRoomId());
            $goodsReleaseIsWaiting->setParentDocumentId($document->getDId());
            $goodsReleaseIsWaiting->setIsWaiting(true);
            $goodsReleaseIsWaiting->setStatusCode(GoodsReleaseDoc::STATUS_NEW);

            $goodsReleaseIsWaiting->setCreatedAt(new \DateTime());        
            $goodsReleaseIsWaiting->setCreatedBy($currentUser->getId());

            $em->persist($goodsReleaseIsWaiting);
            $em->flush($goodsReleaseIsWaiting);

            $goodsReleaseIsWaitingId = $goodsReleaseIsWaiting->getId();

            foreach ($toWaitingRelease as $toWaitingReleaseItem) {
                
                $goodReleaseItemIsWaiting = new GoodsReleaseDocItem();
                $goodReleaseItemIsWaiting->setBaseProductId($toWaitingReleaseItem['baseProductId']);
                $goodReleaseItemIsWaiting->setOrderItemId($toWaitingReleaseItem['orderItemId']);
                $goodReleaseItemIsWaiting->setSupplyItemId($toWaitingReleaseItem['supplyItemId']);
                $goodReleaseItemIsWaiting->setGoodsStateCode();
                $goodReleaseItemIsWaiting->setGoodsReleaseId($goodsReleaseIsWaitingId);
                $goodReleaseItemIsWaiting->setInitialQuantity($toWaitingReleaseItem['initialQuantity']);
                $goodReleaseItemIsWaiting->setQuantity(0);

                $em->persist($goodReleaseItemIsWaiting);
                $em->flush($goodReleaseItemIsWaiting);

            }

        }

        // отметка о проведении докуменнта
        
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();
    }

    private function checkQuantity(\DateTime $actualDate, int $geoRoomId, int $baseProductId, int $supplyItemId, int $quantity, $orderItemId = null, string $goodsStateCode = \AppBundle\Enum\GoodsStateCode::NORMAL, $goodsPalletId = null)
    {
        
        $goodsConditionCode = null;
        $remantOrderItemId = $orderItemId;
        
        // Уточним состояние товара
        $remantQuantity = $this->getReserveRemnant($em, $actualDate, $geoRoomId, $baseProductId, $supplyItemId, $remantOrderItemId, $goodsStateCode, $goodsPalletId);
        
        if ($remantQuantity < $quantity) {
            
            // Не паникуем. Возможно заказ сняли с резервирования, ищем в товаре без привязки к заказазу.                    
            $remantOrderItemId = null;
            $remantQuantity = $this->getReserveRemnant($em, $actualDate, $geoRoomId, $baseProductId, $supplyItemId, $remantOrderItemId, $goodsStateCode, $goodsPalletId);

            if ($remantQuantity < $quantity) {
                // Пора паниковать
                throw new ConflictHttpException('Недостаточно товара '. $baseProductId .'  на складе.');                    
            }else{                        
                $goodsConditionCode = \AppBundle\Enum\GoodsConditionCode::FREE;
            }
            
        }else{
            
            $goodsConditionCode = empty($orderItemId) ? \AppBundle\Enum\GoodsConditionCode::FREE : \AppBundle\Enum\GoodsConditionCode::RESERVED;
            
        }
        
        return [
            'orderItemId' => $remantOrderItemId,
            'goodsConditionCode' => $goodsConditionCode,
            'quantity' => $remantQuantity
        ];
            
    }
    
    private function getReserveRemnant($em, \DateTime $actualDate, int $geoRoomId, int $baseProductId, int $supplyItemId, $orderItemId = null, string $goodsStateCode = \AppBundle\Enum\GoodsStateCode::NORMAL, $goodsPalletId = null): integer
    {

        $rFound = new ResultSetMapping();
        $rFound->addScalarResult('quantity', 'quantity', 'integer');
        
        $queryText = "            
            select
                sum(delta) as quantity
            from goods_reserve_register
            where
                
                gi.goods_release_did = :goodsReleaseDId
            group by
                gi.base_product_id,
                gi.order_item_id,
                gi.supply_item_id,
                gi.goods_pallet_id,
                gi.goods_state_code
            ";
        
    }
    
    private function goodReleaseType2OperationTypeCode($goodsReleaseType): string
    {
        
        switch ($goodsReleaseType) {
            
            case GoodsReleaseType::COURIER:
                return OperationTypeCode::COURIER_DELIVERY;
                
            case GoodsReleaseType::FREIGHT:
                return OperationTypeCode::DELIVERY;

            case GoodsReleaseType::CLIENT:
                return OperationTypeCode::SALE;

            case GoodsReleaseType::ISSUE:
                return OperationTypeCode::GOODS_RELEASE;

            case GoodsReleaseType::MOVEMENT:
                return OperationTypeCode::GOODS_MOVEMENT;

            case GoodsReleaseType::TRANSIT:
                return OperationTypeCode::INNER_TRANSITION;

            default:
                throw new ConflictHttpException('Не описанный тип докмуента отгрузки: '. $goodsReleaseType);
        }
        
    }
    
}

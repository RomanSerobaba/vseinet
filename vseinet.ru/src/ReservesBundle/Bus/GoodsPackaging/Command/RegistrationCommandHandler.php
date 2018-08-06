<?php

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsPackagingType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsPackaging;
use ReservesBundle\Entity\GoodsPackagingItem;
use RegisterBundle\Entity\GoodsReserveRegister;
use ContentBundle\Entity\SupplyItem;

class RegistrationCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(RegistrationCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $goodsPackaging = $em->getRepository(GoodsPackaging::class)->find($command->id);
        if (!$goodsPackaging instanceof GoodsPackaging) {
            throw new NotFoundHttpException('Документ комплектации/разкомплектации не найден');
        }
        
        if (empty($goodsPackaging->getApprovedAt())) {
            throw new ConflictHttpException('Нельзя регистрировать несогласованный документ.');
        }
        
        // отмера регистрайии, если таковая была
        if (!empty($goodsPackaging->getRegistredAt())) {

            $this->get('command_bus')->handle(new UnRegistrationCommand([
                'id' => $command->id
            ]));

        }
        
        // получение списка товаров
        $goodsPackagingItems = $em->getRepository(GoodsPackagingItem::class)->findBy([
            'goodsPackagingDId' => $command->id
        ]);
        if (0 == count($goodsPackagingItems)) {
            throw new ConflictHttpException('Нет списка обрабатываемого товара.');
        }
        
        // Запись шапки документа

        $currentUser = $this->get('user.identity')->getUser();
        
        $goodsPackaging->setRegistredAt(new \DateTime);

        $em->persist($goodsPackaging);
        $em->flush();

        if (GoodsPackagingType::COMBINING == $goodsPackaging->getType()) {

            ///////////////////////////////////////
            //                
            //  Комплектация
            //

            $newPurchaseSumm = 0; //Накопленная стоимость закупкикомпонентов
            $newPushareQuantity = $goodsPackaging->getQuantity();

            // списание компонентов сборки

            foreach ($goodsPackagingItems as $goodsPackagingItemIndex => $goodsPackagingItem) {

                $quantityToOut = $goodsPackagingItem->getQuantityPerOne() * $newPushareQuantity;
                $newPurchaseSumm += $this->writeOffItem($goodsPackagingItem->getBaseProductId(), $quantityToOut, $goodsPackaging, $em);

            }

            // оприходование сборки

            $this->admissionItem($goodsPackaging->getBaseProductId(), $newPushareQuantity, $newPurchaseSumm, $goodsPackaging, $em);

        } else {

            ///////////////////////////////////////
            //                
            //  Разкомплектация
            //

            // списание сборки

            $quantityToOut = $goodsPackaging->getQuantity();
            $newPurchaseSumm = $this->writeOffItem($goodsPackaging->getBaseProductId(), $quantityToOut, $goodsPackaging, $em);

            // оприходование компонентов сборки

            // <editor-fold defaultstate="collapsed" desc="Тексты запросов">

            $rPartsOnWareHouse = new ResultSetMapping();
            $rPartsOnWareHouse->addScalarResult('base_product_id', 'baseProductId', 'integer');
            $rPartsOnWareHouse->addScalarResult('quantity_per_one', 'quantityPerOne', 'integer');
            $rPartsOnWareHouse->addScalarResult('weight', 'weight', 'float');

            $qPartsOnWareHouse = "
                with

                    -- Выбираем элементы раскомплектации с ценами продажи

                    items_with_price as (
                        select
                            gpi.base_product_id,
                            gpi.quantity_per_one,
                            case
                                when p.price is null then gpi.quantity_per_one
                                else p.price * gpi.quantity_per_one
                            end as sum_per_one
                        from goods_packaging_item gpi
                        inner join goods_packaging gp on
                            gp.did = gpi.goods_packaging_did
                        inner join geo_city gc on
                            gc.id = gp.geo_room_id
                        left join product p on
                            p.id = gp.base_product_id
                            and p.geo_city_id is null
                        where
                            gpi.goods_packaging_did = :goodsPackagingDId
                    ),

                    -- Считаем обшую цену продажи
                    all_sum as (
                        select
                            sum(iwp.sum_per_one) as allsum
                        from items_with_price iwp
                    )

                -- определяем вес (коэффициент) для распределения себестоимости
                select
                    t1.base_product_id,
                    t1.quantity_per_one,
                    t1.sum_per_one::float / t2.allsum::float as weight
                from items_with_price t1, all_sum t2
                    order by t1.sum_per_one::float / t2.allsum::float
                ";

            // </editor-fold>

            $newItems = $em->createNativeQuery($qPartsOnWareHouse, $rPartsOnWareHouse)
                    ->setParameters([
                        'goodsPackagingDId' => $goodsPackaging->getDId()
                    ])
                    ->getArrayResult();

            foreach ($newItems as $newItemKey => $newItem) {

                if (count($newItems) == $newItemKey + 1) {
                    // На последний элемент, с самым большим весом, кладём накопленнцую погрешность
                    $nowPurchaseSumm = $newPurchaseSumm;
                }else{
                    $nowPurchaseSumm = round($newPurchaseSumm * $newItem['weight']);
                }

                // оприходование составляющих

                $this->admissionItem($newItem['baseProductId'], $newItem['quantityPerOne'] * $quantityToOut, $nowPurchaseSumm, $goodsPackaging, $em);

                $newPurchaseSumm -= $nowPurchaseSumm;

            }
        }

    }
    
    /**
     * Создание партий (suply_item) и записей в движении товаров (goods_relerve_log) по оприходованию нового товара
     * 
     * @param int                         $baseProductId  идентификатор приходуемого товара
     * @param int                         $addQuantity    количество приходуемого товара
     * @param int                         $addSumm        сумма приходуемого товара
     * @param GoodsPackaging              $goodsPackaging докмент комплектации/разкомплектации
     * @param \Doctrine\ORM\EntityManager $em             менеджер сущностей ORM
     */
    
    public function admissionItem(int $baseProductId, int $addQuantity, int $addSumm, GoodsPackaging $goodsPackaging, \Doctrine\ORM\EntityManager $em)
    {
        
        $currentUser = $this->get('user.identity')->getUser();
        
        // Получаем цену нового товара в копейках
        $firstPartPurchasePrice = (int)($addSumm / $addQuantity);

        // Получаем остаток нераспределённых копеек. Они станут размером второй партии товара.
        $seccondPartQuantity = ($addSumm % $addQuantity);

        // Получаем количество товара, оставшегося в первой партии (по рассчитанной цене)
        $firstPartQuantity = $addQuantity - $seccondPartQuantity;

        // Рассчитываем цену второй партии (увеличенная цена первой партии на копейку)
        $seccondPartPurchasePrice = $firstPartPurchasePrice + 1;

        // first part

        $supplyItem = new SupplyItem();
        $supplyItem->setGoodsPackagingId($goodsPackaging->getId());
        $supplyItem->setBaseProductId($baseProductId);
        $supplyItem->setPurchasePrice($firstPartPurchasePrice);
        $supplyItem->setQuantity($firstPartQuantity);

        $em->persist($supplyItem);
        $em->flush();
        
        $grr = new GoodsReserveRegister();

        $grr->setRegisteredAt($goodsPackaging->getApprovedAt());
        $grr->setCreatedAt(new \DateTime);
        $grr->setCreatedBy($currentUser->getId());
        $grr->setRegisterOperationTypeCode(\AppBundle\Enum\OperationTypeCode::PACKAGING);
        $grr->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_PACKAGING);
        $grr->setRegistratorId($goodsPackaging->getDId());

        $grr->setBaseProductId($baseProductId);
        $grr->setSupplyItemId($supplyItem->getId());
        $grr->setGeoRoomId($goodsPackaging->getGeoRoomId());
        $grr->setGoodsConditionCode(\AppBundle\Enum\GoodsConditionCode::FREE);

        $grr->setDelta($firstPartQuantity);

        $em->persist($grr);
        $em->flush();

        
        // seccond part

        if (0 != $seccondPartQuantity) {

            $supplyItem = new SupplyItem();
            $supplyItem->setGoodsPackagingId($goodsPackaging->getId());
            $supplyItem->setBaseProductId($baseProductId);
            $supplyItem->setPurchasePrice($seccondPartPurchasePrice);
            $supplyItem->setQuantity($seccondPartQuantity);

            $em->persist($supplyItem);
            $em->flush();

            $grr = new GoodsReserveRegister();

            $grr->setRegisteredAt($goodsPackaging->getApprovedAt());
            $grr->setCreatedAt(new \DateTime);
            $grr->setCreatedBy($currentUser->getId());
            $grr->setRegisterOperationTypeCode(\AppBundle\Enum\OperationTypeCode::PACKAGING);
            $grr->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_PACKAGING);
            $grr->setRegistratorId($goodsPackaging->getDId());

            $grr->setBaseProductId($baseProductId);
            $grr->setSupplyItemId($supplyItem->getId());
            $grr->setGeoRoomId($goodsPackaging->getGeoRoomId());
            $grr->setGoodsConditionCode(\AppBundle\Enum\GoodsConditionCode::FREE);

            $grr->setDelta($seccondPartQuantity);

            $em->persist($grr);
            $em->flush();
        }
    }
    
    /**
     * Создание записей в движении товаров (goods_relerve_log) по списанию товара
     * 
     * @param int                         $baseProductId  идентификатор списываемого товара
     * @param int                         $rmQuantity     количество списываемого товара
     * @param GoodsPackaging              $goodsPackaging докмент комплектации/разкомплектации
     * @param \Doctrine\ORM\EntityManager $em             менеджер сущностей ORM
     * 
     * @return int Сумма списанного товара
     */
    
    public function writeOffItem(int $baseProductId, int $rmQuantity, GoodsPackaging $goodsPackaging, \Doctrine\ORM\EntityManager $em)
    {
        
        // <editor-fold defaultstate="collapsed" desc="Тексты запросов">    

        $rPartsOnWareHouse = new ResultSetMapping();
        $rPartsOnWareHouse->addScalarResult('operated_at', 'operatedAt', 'datetime');
        $rPartsOnWareHouse->addScalarResult('base_product_id', 'baseProductId', 'integer');
        $rPartsOnWareHouse->addScalarResult('supply_item_id', 'supplyItemId', 'integer');
        $rPartsOnWareHouse->addScalarResult('purchase_quantity', 'purchaseQuantity', 'integer');
        $rPartsOnWareHouse->addScalarResult('purchase_price', 'purchasePrice', 'integer');
        $rPartsOnWareHouse->addScalarResult('quantity', 'quantity', 'integer');

        $qPartsOnWareHouse = "
            select
                max(grl.registered_at) as registred_at,
                grl.base_product_id,
                grl.supply_item_id,
                si.quantity as purchase_quantity,
                si.purchase_price,                
                sum(delta) as quantity
            from goods_reserve_register grl
            inner join supply_item si on
                si.id = grl.supply_item_id
            where
                grl.registered_at <= :actualDate and
                grl.geo_room_id = :geoRoomId and
                grl.base_product_id = :baseProductId and
                grl.goods_condition_code = 'free'::goods_condition_code
            group by
                grl.base_product_id,
                grl.supply_item_id,
                si.quantity,
                si.purchase_price
            order by
                max(grl.registered_at) DESC,
                grl.supply_item_id ASC
        ";

        // </editor-fold>    
            
        $currentUser = $this->get('user.identity')->getUser();
        
        $addPurchaseSumm = 0; //
        
        // получаем список имеющихся партий свободных остатков
        $supplyItems = $em->createNativeQuery($qPartsOnWareHouse, $rPartsOnWareHouse)
                ->setParameters([
                    'actualDate'    => $goodsPackaging->getApprovedAt(),
                    'geoRoomId'     => $goodsPackaging->getGeoRoomId(),
                    'baseProductId' => $baseProductId
                ])
                ->getArrayResult();

        if (0 == count($supplyItems)) {
            throw new BadRequestHttpException('Нет партий по товару '. $baseProductId .' доступных для списания.');
        }

        foreach ($supplyItems as $supplyItemIndex => $supplyItem) {

            $nowQuantityToOut = $supplyItem['quantity'] > $rmQuantity ? $rmQuantity : $supplyItem['quantity'];

            $grr = new GoodsReserveRegister();

            $grr->setRegisteredAt($goodsPackaging->getApprovedAt());
            $grr->setCreatedAt(new \DateTime);
            $grr->setCreatedBy($currentUser->getId());
            $grr->setRegisterOperationTypeCode(\AppBundle\Enum\OperationTypeCode::PACKAGING);
            $grr->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_PACKAGING);
            $grr->setRegistratorId($goodsPackaging->getDId());

            $grr->setBaseProductId($baseProductId);
            $grr->setSupplyItemId($supplyItem['supplyItemId']);
            $grr->setGeoRoomId($goodsPackaging->getGeoRoomId());
            $grr->setGoodsConditionCode(\AppBundle\Enum\GoodsConditionCode::FREE);

            $grr->setDelta(-$nowQuantityToOut);

            $em->persist($grr);
            $em->flush();

            $rmQuantity -= $nowQuantityToOut;
            $addPurchaseSumm += ($supplyItem['purchasePrice'] * $nowQuantityToOut);

            if ($rmQuantity == 0) {
                break;
            }
        }

        if (0 != $rmQuantity) {
            throw new BadRequestHttpException('Нет достаточного количества товара '. $baseProductId .' доступного для списания.');
        }

        return $addPurchaseSumm;
    }
    
}

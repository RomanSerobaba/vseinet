<?php

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Entity\GoodsReleaseDocItem;

class SetQuantityCommandHandler extends MessageHandler
{
    public function handle(SetQuantityCommand $command) 
    {
        if (('pallet' == $command->type) && (abs($command->quantity) > 1)) 
            throw new BadRequestHttpException('Нельзя оперировать более чем с одной палетой.');

        if (('pallet' == $command->type) && ('normal' != $command->goodsStateCode)) 
            throw new BadRequestHttpException('Подготовленная к отгрузке палета не может быть ненадлежащего качества.');

        $goodsReleaseDoc = $this->getDoctrine()->getManager()->getRepository(GoodsReleaseDoc::class)->find($command->goodsReleaseId);
        if (!$goodsReleaseDoc instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        if (!empty($goodsReleaseDoc->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        }

        if ('pallet' == $command->type) {

            $this->setPallet($command->goodsReleaseId, $command->id, $command->quantity);

        }else{
        
            $this->setProduct($command->goodsReleaseId, $command->id, $command->goodsStateCode, $command->quantity);

        }

        return;
    }

    private function setPallet(int $goodsReleaseDId, int $goodsPalletId, int $quantity)
    {

        if (0 == $quantity) {
            
            $queryText = "
                update
                    goods_release_item gri
                set
                    quantity = 0 
                where
                    gri.goods_release_did = {$goodsReleaseDId}
                    and gri.goods_pallet_id = {$goodsPalletId}
                returning gri.id
            ";
                    
        }else{
            
            $queryText = "
                update
                    goods_release_item gri
                set
                    quantity = gri.initial_quantity 
                where
                    gri.goods_release_did = {$goodsReleaseDId}
                    and gri.goods_pallet_id = {$goodsPalletId}
                returning gri.id
            ";
            
        }
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $rsm)
                ->getArrayResult();

        if (empty($result)) {
            throw new BadRequestHttpException('Палеты не найдены.');
        }

    }

    private function setProduct(int $goodsReleaseDId, int $baseProductId, string $goodsStateCode, int $quantity)
    {

        $queryText = "
            -- Нормализованный список отгрузки без паллет
            select
                row_number() over(order by o.type_code, order_item_id)
                    as ord,                                              -- Синтетический ключ нормализации
                gri.id
            from
                goods_release_item gri
            inner join order_item oi
                on oi.id = gri.order_item_id
            inner join \"order\" o
                on o.id = oi.order_id
            where
                gri.goods_release_did = {$goodsReleaseDId}                        -- Документ отгрузки
                and gri.base_product_id = {$baseProductId}                        -- Идентификатор продукта
                and gri.goods_pallet_id is null                                   -- Идентификатор отсутствия паллеты
                and gri.goods_state_code = '{$goodsStateCode}'::goods_state_code  -- Без претензии
            order by
                o.type_code,
                gri.order_item_id
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ord', 'ord', 'integer');
        $rsm->addScalarResult('id', 'id', 'integer');

        $em = $this->getDoctrine()->getManager();

        $goodsReleaseDocItemsIds = $em
                ->createNativeQuery($queryText, $rsm)
                ->getArrayResult();

        if (empty($goodsReleaseDocItemsIds)) throw new NotFoundHttpException('Продукты не найдены');
            
        foreach ($goodsReleaseDocItemsIds as $goodsReleaseDocItemId) {
            
            $goodsReleaseDocItem = $em->getRepository(GoodsReleaseDocItem::class)->find($goodsReleaseDocItemId['id']);
            
            $outQuantity = min($goodsReleaseDocItem->getInitialQuantity(), $quantity);
            
            $goodsReleaseDocItem->setQuantity($outQuantity);
            
            $em->persist($goodsReleaseDocItem);
            
            $quantity -= $outQuantity;
                    
        }
        
        if (0 < $quantity) throw new BadRequestHttpException('Отгруженное количество не должно быть больше затребованного.');

        $em->flush();
        
    }
    
}
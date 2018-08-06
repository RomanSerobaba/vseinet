<?php

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\AddOn\TempStorage;

class SetCommandHandler extends MessageHandler
{
    use \ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\DeltaActionTrait;

    public function handle(SetCommand $command)
    {

        $em = $this->getDoctrine()->getManager();

        // Проверка наличия документа в базе данных
        $goodsAcceptance = $em->getRepository(GoodsAcceptance::class)->find($command->goodsAcceptanceId);
        if (!$goodsAcceptance instanceof GoodsAcceptance)
            throw new NotFoundHttpException('Документ не найден');
        
        // Проверка статуса документа в базе данных
        if (!empty($goodsAcceptance->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');

        // Подсчет дельты
        
        $queryText = "

            select
                sum(gai.quantity) as quantity
            from
                goods_acceptance_item gai
            left join order_item oi
                on oi.id = gai.order_item_id
            left join \"order\" o
                on o.id = oi.order_id
            where
                gai.goods_acceptance_did = {$command->goodsAcceptanceId}                  -- Документ отгрузки
                and gai.base_product_id = {$command->id}                                  -- Идентификатор продукта
                and gai.goods_state_code = '{$command->goodsStateCode}'::goods_state_code -- Тип дефекта товара
                and o.geo_point_id = {$command->geoPointId}                               -- Направление отгрузки
        ";
                
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('quantity', 'quantity', 'integer');

        $em = $this->getDoctrine()->getManager();

        $result = $em->createNativeQuery($queryText, $rsm)->getResult();

        if (empty($result)) {
            $current = 0;
        }else{
            $current = $result[0]['quantity'];
        }
        
        if ($command->quantity != $current) {
            
            $deltaCommand = new DeltaCommand();
            $deltaCommand->goodsAcceptanceId = $command->goodsAcceptanceId;
            $deltaCommand->id = $command->id;
            $deltaCommand->goodsStateCode = $command->goodsStateCode;
            $deltaCommand->geoPointId = $command->geoPointId;
            $deltaCommand->type = $command->type;
            $deltaCommand->uuid = $command->uuid;
            $deltaCommand->delta = $command->quantity - $current;
            
            $toGeoPoints = $this->runDelta($deltaCommand, $em);
            
        }else{
            
            $toGeoPoints = [];
            
        }
        
        $tempStorage = new TempStorage();
        $tempStorage->setData(json_encode($toGeoPoints), $command->uuid);    
        
        return;
        
    }
    
}

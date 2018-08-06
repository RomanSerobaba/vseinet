<?php

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use Doctrine\ORM\Query\ResultSetMapping;

class DeleteCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(DeleteCommand $command)
    {

        // Проверка наличия документа в базе данных
        $goodsAcceptance = $this->getDoctrine()->getManager()->getRepository(GoodsAcceptance::class)->find($command->goodsAcceptanceId);
        if (!$goodsAcceptance instanceof GoodsAcceptance) throw new NotFoundHttpException('Документ не найден');
        
        // Проверка статуса документа в базе данных
        if (!empty($goodsAcceptance->getCompletedAt())) throw new ConflictHttpException('Изменение завершенного документа невозможно.');

        // Удаление строк табличной части документа
        $queryText = "

            delete from gods_acceptance_item gai
            
            wrere
                gai.goods_acceptance_did = {$command->goodsAcceptanceId}
                and gai.base_product_id = {$command->baseProductId}
                and gai.goods_state_code = '{$command->goodsStateCode}'::goods_state_code
                and gai.geo_point_id = {$command->geoPointId}
                    
            returning gai.id

        ";
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        
        $items = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $rsm)
                ->getArrayResult();

        if (0 == count($items)) {
            throw new BadRequestHttpException('Строки к удалению не найдены.');
        }
        
        return;
    }
    
}

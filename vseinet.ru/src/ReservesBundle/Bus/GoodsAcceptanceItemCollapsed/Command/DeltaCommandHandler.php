<?php

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\AddOn\TempStorage;


class DeltaCommandHandler extends MessageHandler
{
    use \ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\DeltaActionTrait;

    public function handle(DeltaCommand $command)
    {

        $em = $this->getDoctrine()->getManager();

        // Проверка наличия документа в базе данных
        $goodsAcceptance = $em->getRepository(GoodsAcceptance::class)->find($command->goodsAcceptanceId);
        if (!$goodsAcceptance instanceof GoodsAcceptance)
            throw new NotFoundHttpException('Документ не найден');

        // Проверка статуса документа в базе данных
        if (!empty($goodsAcceptance->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');

        $toGeoPoints = $this->runDelta($command, $em);
        $tempStorage = new TempStorage();
        $tempStorage->setData(json_encode($toGeoPoints), $command->uuid);    
        
        return;
    }

}
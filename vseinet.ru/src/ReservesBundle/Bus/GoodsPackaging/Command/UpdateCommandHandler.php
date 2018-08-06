<?php

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsPackagingType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsPackaging;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UpdateCommand $command) 
    {
        $currentUser = $this->get('user.identity')->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $goodsPackaging = $em->getRepository(GoodsPackaging::class)->find($command->id);
        if (!$goodsPackaging instanceof GoodsPackaging) {
            throw new NotFoundHttpException('Документ комплектации/разкомплектации не найден');
        }
        
        // Проверка статуса документа
        if (!empty($goodsPackaging->getCompletedAt())) {
            throw new ConflictHttpException('Удаление утвержденного документа не возможно.');
        }

        // Проверка нового статуса документа
        
        if (!$command->completed) {
            
            $goodsPackaging->setBaseProductId($command->baseProductId);
            $goodsPackaging->setGeoRoomId($command->geoRoomId);
            $goodsPackaging->setQuantity($command->quantity);
            $goodsPackaging->setType($command->type);
            $goodsPackaging->setCompletedAt();
            $goodsPackaging->setCompletedBy();

            $em->persist($goodsPackaging);
            $em->flush();
            
            return;            
        }

        // Закрытие документа, попытка рассчета

        $goodsPackaging->setBaseProductId($command->baseProductId);
        $goodsPackaging->setGeoRoomId($command->geoRoomId);
        $goodsPackaging->setQuantity($command->quantity);
        $goodsPackaging->setType($command->type);
        $goodsPackaging->setCompletedAt(new \DateTime);
        $goodsPackaging->setCompletedBy($currentUser->getId());

        $em->persist($goodsPackaging);
        $em->flush();

        $this->get('command_bus')->handle(new RegistrationCommand([
            'id' => $command->id
        ]));

    }

}
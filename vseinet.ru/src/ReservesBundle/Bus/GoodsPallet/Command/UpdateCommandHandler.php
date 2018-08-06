<?php

namespace ReservesBundle\Bus\GoodsPallet\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsPallet;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(UpdateCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $goodsPallet = $em->getRepository(GoodsPallet::class)->find($command->id);
        if (!$goodsPallet instanceof GoodsPallet) {
            throw new NotFoundHttpException('Паллета не найдена.');
        }
        
        $goodsPallet->setGeoPointId($command->geoPointId);
        $goodsPallet->setTitle($command->title);
        $goodsPallet->setStatus($command->status);

        $em->persist($goodsPallet);
        $em->flush();

        return;
    }

}

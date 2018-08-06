<?php

namespace ContentBundle\Bus\DetailDepend\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $depend = $em->getRepository(Detail::class)->find($command->id);
        if (!$depend instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }

        if (null === $depend->getPid()) {
            throw new BadRequestHttpException('Характеристика должна быть зависимой');
        }

        $depend->setName($command->name);

        $em->persist($depend);
        $em->flush();
    }
}
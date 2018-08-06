<?php

namespace ContentBundle\Bus\ColorPalette\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ColorPalette;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $palette = $em->getRepository(ColorPalette::class)->find($command->id);
        if (!$palette instanceof ColorPalette) {
            throw new NotFoundHttpException('Цветовая палитра не найдена');
        }

        $palette->setName($command->name);

        $em->persist($palette);
        $em->flush();
    }
}
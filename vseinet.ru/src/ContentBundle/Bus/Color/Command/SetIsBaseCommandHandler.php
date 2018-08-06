<?php

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Color;

class SetIsBaseCommandHandler extends MessageHandler
{
    public function handle(SetIsBaseCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $color = $em->getRepository(Color::class)->find($command->id);
        if (!$color instanceof Color) {
            throw new NotFoundHttpException(sprintf('Цвет %d не найден', $command->id));
        }

        $color->setIsBase($command->isBase);

        $em->persist($color);
        $em->flush();
    }
}
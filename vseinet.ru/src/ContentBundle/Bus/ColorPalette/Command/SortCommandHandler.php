<?php

namespace ContentBundle\Bus\ColorPalette\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ColorPalette;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $palette = $em->getRepository(ColorPalette::class)->find($command->id);
        if (!$palette instanceof ColorPalette) {
            throw new NotFoundHttpException(sprintf('Цветовая палитра %d не найдена', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 1;
        } 
        else {
            $under = $em->getRepository(ColorPalette::class)->find($command->underId);
            if (!$under instanceof ColorPalette) {
                throw new NotFoundHttpException(sprintf('Цветовая палитра %d не найдена', $command->underId));
            }
            $sortOrder = $under->getSortOrder();
        }

        if ($sortOrder < $palette->getSortOrder()) {
            // up
            $q = $em->createQuery("
                UPDATE ContentBundle:ColorPalette cp 
                SET cp.sortOrder = cp.sortOrder + 1
                WHERE cp.sortOrder >= :sortOrder
            ");
            $q->setParameter('sortOrder', $sortOrder);
            $q->execute();

            $palette->setSortOrder($sortOrder);
        }
        else {
            // down
            $q = $em->createQuery("
                UPDATE ContentBundle:ColorPalette cp
                SET cp.sortOrder = cp.sortOrder + 1
                WHERE cp.sortOrder > :sortOrder
            ");
            $q->setParameter('sortOrder', $sortOrder);
            $q->execute();

            $palette->setSortOrder($sortOrder + 1);
        }

        $em->persist($palette);
        $em->flush();
    }
}
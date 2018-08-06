<?php 

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Color;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $color = $em->getRepository(Color::class)->find($command->id);
        if (!$color instanceof Color) {
            throw new NotFoundHttpException(sprintf('Цвет %d не найден', $command->id));
        }

        $target = $em->getRepository(Color::class)->find($command->targetId);
        if (!$target instanceof Color) {
            throw new NotFoundHttpException(sprintf('Цвет %d не найден', $command->target));
        }

        if ($target->getSortOrder() < $color->getSortOrder()) {
            // up
            $q = $em->createQuery("
                UPDATE ContentBundle:Color c 
                SET c.sortOrder = c.sortOrder + 1
                WHERE c.paletteId = :paletteId AND c.sortOrder >= :sortOrder
            ");
            $q->setParameter('paletteId', $target->getPaletteId());
            $q->setParameter('sortOrder', $target->getSortOrder());
            $q->execute();

            $color->setSortOrder($target->getSortOrder());
        }
        else {
            // down
            $q = $em->createQuery("
                UPDATE ContentBundle:Color c 
                SET c.sortOrder = c.sortOrder + 1
                WHERE c.paletteId = :paletteId AND c.sortOrder > :sortOrder
            ");
            $q->setParameter('paletteId', $target->getPaletteId());
            $q->setParameter('sortOrder', $target->getSortOrder());
            $q->execute();

            $color->setSortOrder($target->getSortOrder() + 1);
        }

        $em->persist($color);
        $em->flush();
    }
}
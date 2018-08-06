<?php

namespace ContentBundle\Bus\DetailValueAlias\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailValueAlias;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $alias = $em->getRepository(DetailValueAlias::class)->find($command->id);
        if (!$alias instanceof DetailValueAlias) {
            throw new NotFoundHttpException(sprintf('Псевдоним значения характеристики %d не найден', $command->id));
        }

        $em->remove($alias);
        $em->flush();
    }
}
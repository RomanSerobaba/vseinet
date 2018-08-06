<?php

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductNaming;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $naming = $em->getRepository(BaseProductNaming::class)->find($command->id);
        if (!$naming instanceof BaseProductNaming) {
            throw new NotFoundHttpException(sprintf('Элемент формирующий название товара %d не найден', $command->id));
        }
        
        $em->remove($naming);
        $em->flush();
    }
}
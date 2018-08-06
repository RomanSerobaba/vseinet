<?php

namespace ContentBundle\Bus\BaseProductBarCode\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductBarCode;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(BaseProductBarCode::class)->find($command->id);
        
        if (!$item instanceof BaseProductBarCode) {
            throw new NotFoundHttpException('Штрихкод товара не найден');
        }
        
        $em->remove($item);
        $em->flush();
    }
}
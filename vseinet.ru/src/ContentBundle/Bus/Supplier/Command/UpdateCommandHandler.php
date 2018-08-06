<?php

namespace ContentBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\Supplier;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(Supplier::class)->find($command->id);
        if (!$item instanceof Supplier) {
            throw new NotFoundHttpException('Поставщик не найден');
        }
        $item->fill($command);
        $em->persist($item);
        $em->flush();
    }
}
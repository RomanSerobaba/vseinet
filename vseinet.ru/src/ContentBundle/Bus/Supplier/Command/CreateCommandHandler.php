<?php

namespace ContentBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use SupplyBundle\Entity\Supplier;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(Supplier::class)->findOneBy(['code' => $command->code]);
        if ($item instanceof Supplier) {
            throw new ConflictHttpException('Поставщик c таким кодом уже существует');
        }
        
        $uuid = $command->uuid; unset($command->uuid);
        
        $item = new Supplier();
        $item->fill($command);
        $em->persist($item);
        $em->flush();
        
        $this->get('uuid.manager')->saveId($uuid, $item->getId());
    }
}
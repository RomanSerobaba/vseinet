<?php

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($command->id);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->id));
        }

        $em->remove($resource);
        $em->flush();
    }
}
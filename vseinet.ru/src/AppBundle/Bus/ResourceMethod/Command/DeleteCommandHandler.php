<?php

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ResourceMethod;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $method = $em->getRepository(ResourceMethod::class)->find($command->id);
        if (!$method instanceof ResourceMethod) {
            throw new NotFoundHttpException(sprintf('Метод ресурса %d не найден', $command->id));
        }

        $em->remove($method);
        $em->flush();
    }
}
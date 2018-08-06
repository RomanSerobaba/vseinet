<?php

namespace AppBundle\Bus\ResourceGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ResourceGroup;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(ResourceGroup::class)->find($command->id);
        if (!$group instanceof ResourceGroup) {
            throw new NotFoundHttpException(sprintf('Группа ресурсов %d не найдена', $command->id));
        }

        $em->remove($group);
        $em->flush();
    }
}
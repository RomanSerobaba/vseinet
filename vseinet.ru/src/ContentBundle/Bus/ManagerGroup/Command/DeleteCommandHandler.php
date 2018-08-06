<?php 

namespace ContentBundle\Bus\ManagerGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ManagerGroup;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(ManagerGroup::class)->find($command->id);
        if (!$group instanceof ManagerGroup) {
            throw new NotFoundHttpException(sprintf('Группа контент-менеджеров %d не найдена', $command->id));
        }

        $em->remove($group);
        $em->flush();
    }
}
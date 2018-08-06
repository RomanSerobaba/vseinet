<?php 

namespace ContentBundle\Bus\Manager\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Manager;
use ContentBundle\Entity\ManagerGroup;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $manager = $em->getRepository(Manager::class)->find($command->userId);
        if (!$manager instanceof Manager) {
            throw new NotFoundHttpException(sprintf('Контент-менеджер %d не найден', $command->userId));
        }

        if ($command->groupId) {
            $group = $em->getRepository(ManagerGroup::class)->find($command->groupId);
            if (!$group instanceof ManagerGroup) {
                throw new NotFoundHttpException(sprintf('Группа контент-менеджеров %d не найдена', $command->groupId));
            }
        }

        $manager->setGroupId($command->groupId);

        $em->persist($manager);
        $em->flush();
    }
}
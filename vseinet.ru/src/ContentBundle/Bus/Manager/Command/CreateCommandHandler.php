<?php 

namespace ContentBundle\Bus\Manager\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use OrgBundle\Entity\Employee;
use ContentBundle\Entity\Manager;
use ContentBundle\Entity\ManagerGroup;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $employee = $em->getRepository(Employee::class)->find($command->userId);
        if (!$employee instanceof Employee) {
            throw new NotFoundHttpException(sprintf('Сотрудник %d не найден', $command->userId));
        }

        if ($command->groupId) {
            $group = $em->getRepository(ManagerGroup::class)->find($command->groupId);
            if (!$group instanceof ManagerGroup) {
                throw new NotFoundHttpException(sprintf('Группа контент-менеджеров %d не найдена', $command->groupId));
            }
        }

        $manager = $em->getRepository(Manager::class)->find($command->userId);
        if (!$manager instanceof Manager) {
            $manager = new Manager();
        }
        $manager->setUserId($command->userId);
        $manager->setGroupId($command->groupId);
        $manager->setIsActive(true);

        $em->persist($manager);
        $em->flush();
    }
}
<?php

namespace OrgBundle\Bus\DepartmentType\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\DepartmentType;
use OrgBundle\Entity\DepartmentTypeEmployeeActivity;

class DeleteDepartmentTypeActivityCommandHandler extends MessageHandler
{
    /**
     * @param DeleteDepartmentTypeActivityCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(DeleteDepartmentTypeActivityCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DepartmentType $departmentType */
        $departmentType = $em->getRepository(DepartmentType::class)->find($command->departmentTypeId);

        if (!$departmentType)
            throw new EntityNotFoundException('Нет такого типа подразделения');


        /** @var DepartmentTypeEmployeeActivity $activity */
        $activity = $em->getRepository(DepartmentTypeEmployeeActivity::class)->find($command->activityId);

        if (!$departmentType)
            throw new EntityNotFoundException('Нет такого показателя для сотрудника заданного типа подразделения');

        $em->remove($activity);
        $em->flush();
    }
}
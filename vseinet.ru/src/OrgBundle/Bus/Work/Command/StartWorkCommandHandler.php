<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;

class StartWorkCommandHandler extends MessageHandler
{
    /**
     * @param StartWorkCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(StartWorkCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($currentUser->getId());

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        if ($employee->getClockInTime() && ($employee->getClockInTime()->format('Y-m-d') != date('Y-m-d'))) {
            $employee->setClockInTime(null);
            $em->persist($employee);
            $em->flush();
        }

        if (!$employee->getClockInTime()) {
            $employee->setClockInTime(new \DateTime());
            $em->persist($employee);
            $em->flush();
        }
    }
}
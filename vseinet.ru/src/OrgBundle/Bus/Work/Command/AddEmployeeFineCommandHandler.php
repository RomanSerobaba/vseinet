<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeFine;

class AddEmployeeFineCommandHandler extends MessageHandler
{
    /**
     * @param AddEmployeeFineCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(AddEmployeeFineCommand $command)
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
        $employee = $em->getRepository(Employee::class)->find($command->id);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        if (!$command->date)
            $command->date = date('Y-m-d');

        $fine = new EmployeeFine();
        $fine->setEmployeeId($employee->getUserId());
        $fine->setType(EmployeeFine::TYPE_MISCELLANEOUS);
        $fine->setDate(new \DateTime($command->date));

        $fine->setStatus(EmployeeFine::STATUS_CREATED);
        $fine->setStatusChangedBy($currentUser->getId());
        $fine->setStatusChangedAt(new \DateTime());

        $fine->setAmount($command->amount);
        $fine->setCause($command->cause);

        $em->persist($fine);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $fine->getId());
    }
}
<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\EmployeeCancelRequest;
use OrgBundle\Entity\EmployeeFine;

class SetEmployeeFineIsDeclinedCommandHandler extends MessageHandler
{
    /**
     * @param SetEmployeeFineIsDeclinedCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(SetEmployeeFineIsDeclinedCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /** @var EmployeeFine $fine */
        $fine = $em->getRepository(EmployeeFine::class)->find($command->id);

        if (!$fine)
            throw new EntityNotFoundException('(Де)премирование не найдено');

        if ($fine->getType() && in_array($fine->getType(), [EmployeeFine::TYPE_ABSENCE, EmployeeFine::TYPE_UNWORKING])) {

            /** @var EmployeeCancelRequest $cancelRequest */
            $cancelRequest = $fine->getCancelRequest();

            if (!$cancelRequest) {
                $cancelRequest = new EmployeeCancelRequest();

                $cancelRequest->setFine($fine);
                $cancelRequest->setCause('Отмена нарушения');
                $cancelRequest->setStatus(EmployeeFine::STATUS_CREATED);
            }

            if ($command->value) {
                if ($cancelRequest->getStatus() != EmployeeFine::STATUS_CANCELED) {
                    $cancelRequest->setStatus(EmployeeFine::STATUS_CANCELED);
                    $cancelRequest->setStatusChangedBy($currentUser->getId());
                    $cancelRequest->setStatusChangedAt(new \DateTime());
                }
            } elseif ($cancelRequest->getStatus() == EmployeeFine::STATUS_CANCELED) {
                $cancelRequest->setStatus(EmployeeFine::STATUS_CREATED);
                $cancelRequest->setStatusChangedBy($currentUser->getId());
                $cancelRequest->setStatusChangedAt(new \DateTime());
            }

            $em->persist($cancelRequest);

        } else {
            if ($command->value) {
                if ($fine->getStatus() != EmployeeFine::STATUS_CANCELED) {
                    $fine->setStatus(EmployeeFine::STATUS_CANCELED);
                    $fine->setStatusChangedBy($currentUser->getId());
                    $fine->setStatusChangedAt(new \DateTime());
                }
            } elseif ($fine->getStatus() == EmployeeFine::STATUS_CANCELED) {
                $fine->setStatus(EmployeeFine::STATUS_CREATED);
                $fine->setStatusChangedBy($currentUser->getId());
                $fine->setStatusChangedAt(new \DateTime());
            }
        }

        $em->persist($fine);
        $em->flush();
    }
}
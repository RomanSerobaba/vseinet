<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($command->id);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }

        if ($detail->getGroupId() != $command->groupId) {
            $group = $em->getRepository(DetailGroup::class)->find($command->groupId);
            if (!$group instanceof DetailGroup) {
                throw new NotFoundHttpException(sprintf('Группа характеристик %d не найдена', $command->groupId));
            }
            $detail->setGroupId($group->getId());
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        } 
        else {
            $under = $em->getRepository(Detail::class)->find($command->underId);
            if (!$under instanceof Detail) {
                throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->underId));
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $em->createQuery("
            UPDATE ContentBundle:Detail d 
            SET d.sortOrder = d.sortOrder + 1
            WHERE d.groupId = :groupId AND d.sortOrder > :sortOrder
        ");
        $q->setParameter('groupId', $detail->getGroupId());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();
        
        $detail->setSortOrder($sortOrder + 1);

        $em->persist($detail);
        $em->flush();
    }
}
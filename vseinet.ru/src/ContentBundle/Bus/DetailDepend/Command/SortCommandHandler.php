<?php

namespace ContentBundle\Bus\DetailDepend\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $depend = $em->getRepository(Detail::class)->find($command->id);
        if (!$depend instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Дочерняя характеристика %d не найдена', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        } 
        else {
            $under = $em->getRepository(Detail::class)->find($command->underId);
            if (!$under instanceof Detail) {
                throw new NotFoundHttpException(sprintf('Дочерняя характеристика %d не найдена', $command->underId));
            }
            if ($depend->getPid() != $under->getPid()) {
                throw new BadRequestHttpException('Сортировка дочерних характеристик возможна только в пределах родительской');
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:Detail d
            SET d.sortOrder = d.sortOrder + 1
            WHERE d.pid = :pid AND d.sortOrder > :sortOrder
        ");
        $q->setParameter('pid', $depend->getPid());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();
        
        $depend->setSortOrder($sortOrder + 1);

        $em->persist($depend);
        $em->flush();
    }
}
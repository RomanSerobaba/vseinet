<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\DetailGroup;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(DetailGroup::class)->find($command->id);
        if (!$group instanceof DetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик %d  не найдена', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        } 
        else {
            $under = $em->getRepository(DetailGroup::class)->find($command->underId);
            if (!$under instanceof DetailGroup) {
                throw new NotFoundHttpException(sprintf('Группа характеристик %d  не найдена', $command->underId));
            }
            if ($group->getCategoryId() != $under->getCategoryId()) {
                throw new BadRequestHttpException('Сортируемые группы характеристик должны быть в одной категории');
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $em->createQuery("
            UPDATE ContentBundle:DetailGroup dg
            SET dg.sortOrder = dg.sortOrder + 1
            WHERE dg.categoryId = :categoryId AND dg.sortOrder > :sortOrder
        ");
        $q->setParameter('categoryId', $group->getCategoryId());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();

        $group->setSortOrder($sortOrder + 1);

        $em->persist($group);
        $em->flush();
    }
}
<?php

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\BaseProductNaming;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $naming = $em->getRepository(BaseProductNaming::class)->find($command->id);
        if (!$naming instanceof BaseProductNaming) {
            throw new NotFoundHttpException(sprintf('Элемент наименования %s не найден', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        } 
        else {
            $under = $em->getRepository(BaseProductNaming::class)->find($command->underId);
            if (!$under instanceof BaseProductNaming) {
                throw new NotFoundHttpException(sprintf('Элемент наименования %s не найден', $command->underId));
            }
            if ($under->getCategoryId() != $naming->getCategoryId()) {
                throw new BadRequestHttpException('Сортировать элементы наименования можно только в пределах одной категории');
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:BaseProductNaming bpn
            SET bpn.sortOrder = bpn.sortOrder + 1
            WHERE bpn.categoryId = :categoryId AND bpn.sortOrder > :sortOrder
        ");
        $q->setParameter('categoryId', $naming->getCategoryId());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();

        $naming->setSortOrder($sortOrder + 1);

        $em->persist($naming);
        $em->flush();
    }
}
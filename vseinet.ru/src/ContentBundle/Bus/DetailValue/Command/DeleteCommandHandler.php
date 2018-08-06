<?php

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\DetailValue;
use ContentBundle\Entity\DetailValueAlias;
use ContentBundle\Entity\DetailToProduct;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $value = $em->getRepository(DetailValue::class)->find($command->id);
        if (!$value instanceof DetailValue) {
            throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $command->id));
        }

        $q = $em->createQuery("
            DELETE FROM ContentBundle:DetailValueAlias dva
            WHERE dva.valueId = :valueId 
        ");
        $q->setParameter('valueId', $value->getId());
        $q->execute();
        
        $q = $em->createQuery("
            DELETE FROM ContentBundle:DetailToProduct d2p 
            WHERE d2p.valueId = :valueId 
        ");
        $q->setParameter('valueId', $value->getId());
        $q->execute();

        $em->remove($value);
        $em->flush();
    }
}
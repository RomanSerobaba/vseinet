<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($command->id);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }

        if ('memo' == $detail->getTypeCode()) {
            $q = $em->createQuery("
                DELETE FROM ContentBundle:DetailMemoToProduct dm2p 
                WHERE dm2p.detailId = :detailId
            ");
            $q->setParameter('detailId', $detail->getId());
            $q->execute();
        }
        else {
            $q = $em->createQuery("
                DELETE FROM ContentBundle:DetailToProduct d2p 
                WHERE d2p.detailId = :detailId OR d2p.detailId = :pid
            ");
            $q->setParameter('detailId', $detail->getId());
            $q->setParameter('pid', $detail->getId());
            $q->execute();

            $q = $em->createQuery("
                DELETE FROM ContentBundle:DetailValue dv 
                WHERE dv.detailId = :detailId OR dv.detailId = :pid
            ");
            $q->setParameter('detailId', $detail->getId());
            $q->setParameter('pid', $detail->getId());
            $q->execute();

            $q = $em->createQuery("
                DELETE FROM ContentBundle:Detail d 
                WHERE d.pid = :pid 
            ");
            $q->setParameter('pid', $detail->getId());
            $q->execute();
        }

        $em->remove($detail);
        $em->flush();
    }
}
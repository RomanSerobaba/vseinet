<?php 

namespace ContentBundle\Bus\ParserDetail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserDetail;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\ParserDetailToContentDetail;

class DetachCommandHandler extends MessageHandler
{
    public function handle(DetachCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $parserDetail = $em->getRepository(ParserDetail::class)->find($command->id);
        if (!$parserDetail instanceof ParserDetail) {
            throw new NotFoundHttpException(sprintf('Характеристика парсера %s не найдена', $command->id));
        }

        $detail = $em->getRepository(Detail::class)->find($command->detailId);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %s не найдена', $command->detailId));
        }

        $q = $em->createQuery("
            DELETE FROM ContentBundle:ParserDetailToContentDetail pd2cd 
            WHERE pd2cd.parserDetailId = :parserDetailId AND pd2cd.contentDetailId = :contentDetailId
        ");
        $q->setParameter('parserDetailId', $parserDetail->getId());
        $q->setParameter('contentDetailId', $detail->getId());
        $q->execute();

        $em->flush();
    }
}

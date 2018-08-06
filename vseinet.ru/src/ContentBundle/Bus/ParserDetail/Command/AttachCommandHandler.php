<?php 

namespace ContentBundle\Bus\ParserDetail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserDetail;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\ParserDetailToContentDetail;

class AttachCommandHandler extends MessageHandler
{
    public function handle(AttachCommand $command)
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

        $relation = new ParserDetailToContentDetail();
        $relation->setParserDetailId($parserDetail->getId());
        $relation->setContentDetailId($detail->getId());

        $em->persist($relation);
        $em->flush();
    }
}

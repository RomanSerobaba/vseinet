<?php 

namespace ContentBundle\Bus\ParserDetail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserDetail;

class SetIsHiddenCommandHandler extends MessageHandler
{
    public function handle(SetIsHiddenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(ParserDetail::class)->find($command->id);
        if (!$detail instanceof ParserDetail) {
            throw new NotFoundHttpException(sprintf('Характеристика парсера %s не найдена', $command->id));
        }

        $detail->setIsHidden($command->value);

        $em->persist($detail);
        $em->flush();
    }
}

<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;

class SetIsActiveCommandHandler extends MessageHandler
{
    public function handle(SetIsActiveCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $source = $em->getRepository(ParserSource::class)->find($command->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s  не найден', $command->id));
        }

        $source->setIsActive($command->isActive);

        $em->persist($source);
        $em->flush();
    }
}
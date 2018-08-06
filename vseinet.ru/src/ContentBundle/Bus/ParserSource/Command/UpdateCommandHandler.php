<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $source = $em->getRepository(ParserSource::class)->find($command->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s  не найден', $command->id));
        }

        $source->setCode($command->code);
        $source->setAlias($command->alias);
        $source->setUrl($command->url);
        $source->setUseAntiGuard($command->useAntiGuard);
        $source->setIsParseImages($command->isParseImages);
        $source->setIsActive($command->isActive);

        $em->persist($source);
        $em->flush();
    }
}
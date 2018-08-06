<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\ParserSource;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $source = $em->getRepository(ParserSource::class)->find($command->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s  не найден', $command->id));
        }

        $em->remove($source);
        $em->flush();
    }
}
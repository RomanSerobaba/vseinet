<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;

/**
 * @deprecated
 */
class SendCodeCommandHandler extends MessageHandler
{
    public function handle(SendCodeCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $source = $em->getRepository(ParserSource::class)->find($command->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s  не найден', $command->id));
        }

        // @todo : отправка сода парсера клиентам
    }
}
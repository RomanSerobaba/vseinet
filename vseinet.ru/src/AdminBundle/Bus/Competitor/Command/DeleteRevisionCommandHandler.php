<?php

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\CompetitorProduct;

class DeleteRevisionCommandHandler extends MessageHandler
{
    public function handle(DeleteRevisionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $revision = $em->getRepository(CompetitorProduct::class)->find($command->id);
        if (!$revision instanceof CompetitorProduct) {
            throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $command->id));
        }

        $em->remove($revision);
        $em->flush();
    }
}

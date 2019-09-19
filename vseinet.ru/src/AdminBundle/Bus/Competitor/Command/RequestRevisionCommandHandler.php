<?php

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\CompetitorProduct;

class RequestRevisionCommandHandler extends MessageHandler
{
    public function handle(RequestRevisionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $revision = $em->getRepository(CompetitorProduct::class)->find($command->id);
        if (!$revision instanceof CompetitorProduct) {
            throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $command->id));
        }

        $revision->setRequestedAt(new \DateTime());
        $revision->setIsManualRequest(true);

        $em->persist($revision);
        $em->flush();
    }
}

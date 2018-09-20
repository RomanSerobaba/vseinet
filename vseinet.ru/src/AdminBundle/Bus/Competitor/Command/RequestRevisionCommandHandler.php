<?php 

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ProductToCompetitor;

class RequestRevisionCommandHandler extends MessageHandler
{
    public function handle(RequestRevisionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $revision = $em->getRepository(ProductToCompetitor::class)->find($command->id);
        if (!$revision instanceof ProductToCompetitor) {
            throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $command->id));
        }

        $revision->setRequestedAt(new \DateTime());

        $em->persist($revision);
        $em->flush();
    }
}

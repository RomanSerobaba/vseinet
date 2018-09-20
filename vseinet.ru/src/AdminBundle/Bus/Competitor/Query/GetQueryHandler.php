<?php 

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ProductToCompetitor;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $revision = $em->getRepository(ProductToCompetitor::class)->find($query->id);
        if (!$revision instanceof ProductToCompetitor) {
            throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $query->id));
        }

        return $revision;
    }
}

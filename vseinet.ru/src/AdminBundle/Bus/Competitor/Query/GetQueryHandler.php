<?php

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\CompetitorProduct;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $revision = $em->getRepository(CompetitorProduct::class)->find($query->id);
        if (!$revision instanceof CompetitorProduct) {
            throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $query->id));
        }

        return $revision;
    }
}

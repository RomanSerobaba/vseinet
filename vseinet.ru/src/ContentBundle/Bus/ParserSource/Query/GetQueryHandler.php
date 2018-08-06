<?php 

namespace ContentBundle\Bus\ParserSource\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $source = $this->getDoctrine()->getManager()->getRepository(ParserSource::class)->find($query->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s не найден', $query->id));
        }

        return $source;
    }
}
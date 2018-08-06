<?php 

namespace AppBundle\Bus\ApiMethodSection\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ApiMethodSection;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $section = $this->getDoctrine()->getManager()->getRepository(ApiMethodSection::class)->find($query->id);
        if (!$section instanceof ApiMethodSection) {
            throw new NotFoundHttpException(sprintf('Раздел API %d не найден', $query->id));
        }

        return $section;
    }
}
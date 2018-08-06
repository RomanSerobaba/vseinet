<?php 

namespace AppBundle\Bus\Resource\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $resource = $this->getDoctrine()->getManager()->getRepository(Resource::class)->find($query->id);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $query->id));
        }

        return $resource;
    }
}
<?php 

namespace AppBundle\Bus\ApiMethod\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ApiMethod;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $method = $this->getDoctrine()->getManager()->getRepository(ApiMethod::class)->find($query->id);
        if (!$method instanceof ApiMethod) {
            throw new NotFoundHttpException(sprintf('Метод API %d не найден', $query->id));
        }

        return $method;
    }
}
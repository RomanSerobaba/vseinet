<?php 

namespace AppBundle\Bus\ResourceMethod\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\ResourceMethod\Query\DTO\Method;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\ResourceMethod\Query\DTO\Method (
                    rm.id,
                    am.name,
                    rm.resourceId,
                    am.method,
                    am.path,
                    am.parameters,
                    am.responses,
                    am.description,
                    rm.apiMethodId
                )
            FROM AppBundle:ResourceMethod rm 
            INNER JOIN AppBundle:ApiMethod am WITH am.id = rm.apiMethodId 
            WHERE rm.id = :id 
        ");
        $q->setParameter('id', $query->id);
        $method = $q->getOneOrNullResult();
        if (!$method instanceof Method) {
            throw new NotFoundHttpException(sprintf('Метод ресурса %d не найден', $query->id));
        }

        return $method;
    }
}
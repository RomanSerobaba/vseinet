<?php 

namespace AppBundle\Bus\ResourceMethod\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($query->resourceId);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $query->resourceId));
        }

        $q = $em->createQuery("
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
                ),
                ams.name HIDDEN ORD 
            FROM AppBundle:ResourceMethod rm 
            INNER JOIN AppBundle:ApiMethod am WITH am.id = rm.apiMethodId 
            INNER JOIN AppBundle:ApiMethodSection ams WITH ams.id = am.sectionId 
            WHERE rm.resourceId = :resourceId
            ORDER BY ORD, am.sortOrder ASC 
        ");
        $q->setParameter('resourceId', $resource->getId());

        return $q->getArrayResult();
    }
}
<?php 

namespace AppBundle\Bus\ResourceMethod\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;

class GetRoleCodexQueryHandler extends MessageHandler
{
    public function handle(GetRoleCodexQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($query->resourceId);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $query->resourceId));
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\ResourceMethod\Query\DTO\RoleCodexItem ( 
                    sr.id,
                    rm.id,
                    CASE WHEN EXISTS (
                        SELECT 1
                        FROM AppBundle:ApiMethodSubroleCodex amrc
                        WHERE amrc.subroleId = sr.id AND amrc.apiMethodId = rm.apiMethodId 
                    ) 
                    THEN true ELSE false END
                )
            FROM AppBundle:Subrole sr, AppBundle:ResourceMethod rm
            WHERE rm.resourceId = :resourceId 
        ");
        $q->setParameter('resourceId', $resource->getId());
        $items = $q->getArrayResult();

        $codex = [];
        foreach ($items as $item) {
            $codex[$item->apiMethodId][$item->subroleId] = $item->isAllowed;
        }

        return $codex;
    }
}
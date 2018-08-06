<?php 

namespace AppBundle\Bus\Resource\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;

class GetAccessRightsQueryHandler extends MessageHandler
{
    public function handle(GetAccessRightsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($query->id);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $query->id));
        }

        $q = $em->createQuery("
            SELECT 
                am.accessRight
            FROM AppBundle:ApiMethodSubroleCodex amsrc 
            INNER JOIN AppBundle:ApiMethod am WITH am.id = amsrc.apiMethodId 
            INNER JOIN AppBundle:UserToSubrole u2s WITH u2s.subroleId = amsrc.subroleId
            INNER JOIN AppBundle:ResourceMethod rm WITH rm.apiMethodId = am.id 
            WHERE u2s.userId = :userId AND rm.resourceId = :resourceId 
            GROUP BY am.id
        ");
        $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
        $q->setParameter('resourceId', $resource->getId());
        $accessRights = array_fill_keys($q->getResult('ListHydrator'), true);

        $q = $em->createQuery("
            SELECT 
                am.accessRight,
                amuc.isAllowed 
            FROM AppBundle:ApiMethodUserCodex amuc
            INNER JOIN AppBundle:ApiMethod am WITH am.id = amuc.apiMethodId
            INNER JOIN AppBundle:ResourceMethod rm WITH rm.apiMethodId = am.id
            WHERE amuc.userId = :userId AND rm.resourceId = :resourceId 
        ");
        $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
        $q->setParameter('resourceId', $resource->getId());
        foreach ($q->getResult('ListHydrator') as $accessRight => $isAllowed) {
            if ($isAllowed) {
                $accessRights[$accessRight] = true;
            }
            elseif ('dev' == $this->getParameter('kernel.environment')) {
                $accessRights[$accessRight] = false;
            }
            else {
                unset($accessRights[$accessRight]);
            }
        }

        if (empty($accessRights)) {
            return null;
        }

        return $accessRights;
    }
}
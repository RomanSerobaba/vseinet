<?php 

namespace AppBundle\Bus\Resource\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetRoleCodexQueryHandler extends MessageHandler
{
    public function handle(GetRoleCodexQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Resource\Query\DTO\RoleCodexItem (
                    sr.id,
                    r.id,
                    CASE WHEN EXISTS (
                        SELECT 1
                        FROM AppBundle:ResourceSubroleCodex rsrc
                        WHERE rsrc.subroleId = sr.id AND rsrc.resourceId = r.id 
                    ) 
                    THEN true ELSE false END
                )
            FROM AppBundle:Subrole sr, AppBundle:Resource r 
        ");
        $items = $q->getArrayResult();
        
        $codex = [];
        foreach ($items as $item) {
            $codex[$item->resourceId][$item->subroleId] = $item->isAllowed;
        }

        return $codex;
    }
}
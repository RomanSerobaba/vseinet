<?php 

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\GeoRegion;

class GetRegionsQueryHandler extends MessageHandler
{
    public function handle(GetRegionsQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT gr 
            FROM AppBundle:GeoRegion AS gr 
            WHERE EXISTS (
                SELECT 1 
                FROM AppBundle:GeoCity AS gc 
                WHERE gc.geoRegionId = gr.id AND gc.isListed = true
            )
            ORDER BY gr.name
        ");    

        return $q->getResult();
    }
}

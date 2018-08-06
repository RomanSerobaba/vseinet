<?php 

namespace ContentBundle\Bus\GeoRoom\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {

        $setParameters = [];
        
        $queryText = "
            SELECT DISTINCT
                gr
            FROM ContentBundle:GeoRoom gr
            WHERE 1=1";

        if (isset($query->inGeoRoomTypes)) {
            
            $queryText .= "
                AND gr.type IN (:inGeoRoomTypes)";
            
            $setParameters['inGeoRoomTypes'] = $query->inGeoRoomTypes;

        }
        
        if (isset($query->inGeoPoints)) {
            
            $queryText .= "
                AND gr.geoPointId IN (:inGeoPoints)";
            
            $setParameters['inGeoPoints'] = $query->inGeoPoints;

        }
        
        $queryText .= "
            ORDER BY gr.name";

        $queryDB = $this->getDoctrine()->getManager()->createQuery($queryText);
        
        // Пагинация
        
        if ($query->limit) {
            $queryDB->
                    setMaxResults($query->limit);
        }
        if ($query->page) {
            $queryDB->
                    setFirstResult($query->page * $query->limit);
        }
        
        /////////////////////////////////////
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
            
        return $queryDB->getResult();
    }
    
}
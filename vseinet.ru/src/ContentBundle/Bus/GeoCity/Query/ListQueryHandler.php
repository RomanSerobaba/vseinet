<?php 

namespace ContentBundle\Bus\GeoCity\Query;

use AppBundle\Bus\Message\MessageHandler;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {

        $setParameters = [];
        
        if (isset($query->onlyWithGeoRoom)) {
            
            $queryText = "
                SELECT DISTINCT
                    gc
                FROM ContentBundle:GeoRoom gr
                INNER JOIN ContentBundle:GeoPoint gp WITH gp.id = gr.geoPointId
                INNER JOIN ContentBundle:GeoCity gc WITH gc.id = gp.geoCityId
                ";
            
        }else{
            
            $queryText = "
                SELECT
                    gc
                FROM ContentBundle:GeoCity gc
                ";
            
        }

        if (isset($query->inGeoRegiones)) {
            
            $queryText .= "
                WHERE gc.geoRegionId IN (:inGeoRegiones)";
            
            $setParameters['inGeoRegiones'] = $query->inGeoRegiones;

        }
        
        $queryText .= "
            ORDER BY gc.name";

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
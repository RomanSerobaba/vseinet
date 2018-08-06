<?php 

namespace ContentBundle\Bus\GeoPoint\Query;

use AppBundle\Bus\Message\MessageHandler;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {

        if (isset($query->inGeoPointTypes)) {
            
            if (!preg_match('/^\[ *\"[^\"]*\"( *, *\"[^\"]*\")* *]/', $query->inGeoPointTypes)) {
                throw new BadRequestHttpException('Неверный формат списка типов.');
            }

            $query->inGeoPointTypes = json_decode($query->inGeoPointTypes, true);

        }
        
        if (!empty($query->inGeoCities)) {

            if (!preg_match('/^\[ *\d+( *, *\d+)* *\]/', $query->inGeoCities)) {
                throw new BadRequestHttpException('Неверный формат списка идентификаторов.');
            }

            $query->inGeoCities = json_decode($query->inGeoCities, true);

        }
        
        $setParameters = [];
        
        if (isset($query->onlyWithGeoRoom)) {
            
            $queryText = "
                SELECT DISTINCT
                    gp
                FROM ContentBundle:GeoRoom gr
                INNER JOIN ContentBundle:GeoPoint gp WITH gp.id = gr.geoPointId
                WHERE 1=1";
            
        }else{
            
            $queryText = "
                SELECT
                    gp
                FROM ContentBundle:GeoPoint gp
                WHERE 1=1";
            
        }

        if (isset($query->inGeoPointTypes)) {
            
            $queryText .= "
                AND gp.type IN (:inGeoPointTypes)";
            
            $setParameters['inGeoPointTypes'] = $query->inGeoPointTypes;

        }
        
        if (isset($query->inGeoCities)) {
            
            $queryText .= "
                AND gp.geoCityId IN (:inGeoCities)";
            
            $setParameters['inGeoCities'] = $query->inGeoCities;

        }
        
        $queryText .= "
            ORDER BY gp.name";

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
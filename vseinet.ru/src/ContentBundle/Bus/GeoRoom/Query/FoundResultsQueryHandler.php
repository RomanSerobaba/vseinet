<?php 

namespace ContentBundle\Bus\GeoRoom\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FoundResultsQueryHandler extends MessageHandler
{
    public function handle(FoundResultsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        
        $setParameters = [];
        
        $queryText = "
            SELECT DISTINCT
                NEW ContentBundle\Bus\GeoRoom\Query\DTO\FoundResults(
                    gr.id,
                    gr.name,
                    gp.name,
                    gc.name
                )
            FROM ContentBundle:GeoRoom gr
            INNER JOIN ContentBundle:GeoPoint gp WITH gp.id = gr.geoPointId
            INNER JOIN ContentBundle:GeoCity gc WITH gc.id = gp.geoCityId
            WHERE 
                1 = 1";
            
        if (!empty($query->inGeoRoomTypes)) {
            
            $queryText .= "
               AND  gr.type IN (:inGeoRoomTypes)";
            
            $setParameters['inGeoRoomTypes'] = $query->inGeoRoomTypes;
            
        }
        
        if (!empty($query->inGeoCityes)) {
            
            $queryText .= "
                AND gp.geoCityId IN (:inGeoCityes)";
            
            $setParameters['inGeoCityes'] = $query->inGeoCityes;

        }
        
        if (!empty($query->inGeoPoints)) {
            
            $queryText .= "
                AND gr.geoPointId IN (:inGeoPoints)";
            
            $setParameters['inGeoPoints'] = $query->inGeoPoints;

        }
        
        if (!empty($query->q)) {
            
            $queryText .= "
                AND (
                    lower(gr.name) LIKE :needString OR
                    lower(gp.name) LIKE :needString OR
                    lower(gc.name) LIKE :needString
                )";
            
            $setParameters['needString'] = '%'. mb_strtolower($query->q) .'%';

        }
        
        $queryText .= "
            ORDER BY gc.name, gp.name, gr.name";

        $queryDB = $em->createQuery($queryText);
        
        if ($query->limit) {
            $queryDB->
                    setMaxResults($query->limit);
        }
        
        /////////////////////////////////////
        
        $queryDB->setParameters($setParameters);
            
        return $queryDB->getResult();
    }
    
}
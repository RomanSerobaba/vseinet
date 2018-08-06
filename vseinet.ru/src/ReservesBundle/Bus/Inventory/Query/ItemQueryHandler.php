<?php 

namespace ReservesBundle\Bus\Inventory\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $currentUser = $this->get('user.identity')->getUser();
        
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\Inventory\Query\DTO\InventoryItem(
                    i.dId,
                    i.number,
                    i.createdAt,
                    i.createdBy,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname, ' ') end,
                        case when pc.secondname is null then '' else concat(pc.secondname, ' ') end,
                        case when pc.lastname is null   then '' else concat(pc.lastname, ' ') end),
                    i.title,
                    i.geoRoomId,
                    concat(case when gp.name is null then '' else concat(gp.name, ', ') end, gr.name),
                    i.responsibleId,
                    concat(
                        case when pr.firstname is null  then '' else concat(pr.firstname, ' ') end,
                        case when pr.secondname is null then '' else concat(pr.secondname, ' ') end,
                        case when pr.lastname is null   then '' else concat(pr.lastname, ' ') end),
                    i.completedAt,
                    i.status,
                    i.categories,
                    0
                )
            FROM ReservesBundle:Inventory i
            LEFT JOIN AppBundle:User uc WITH i.createdBy = uc.id  
            LEFT JOIN AppBundle:Person pc WITH uc.personId = pc.id  
            LEFT JOIN AppBundle:User ur WITH i.responsibleId = ur.id  
            LEFT JOIN AppBundle:Person pr WITH ur.personId = pr.id  
            LEFT JOIN OrgBundle\Entity\GeoRoom gr WITH gr.id = i.geoRoomId
            LEFT JOIN OrgBundle\Entity\GeoPoint gp WITH gp.id = gr.geoPointId
            WHERE i.dId = :inventoryDId";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameters([
                    'inventoryDId' => $query->id
                ]);
        
        $result = $queryDB->getArrayResult();
        if (count($result) == 0) throw new NotFoundHttpException('Докмент не найден');


        $queryTextParticipants = "
            SELECT 
               ip.participantId
            FROM ReservesBundle:InventoryParticipant ip
            LEFT JOIN AppBundle:User uc WITH ip.participantId = uc.id  
            LEFT JOIN AppBundle:Person pp WITH uc.personId = pp.id
            WHERE ip.inventoryDId = :inventoryDId";

        $queryDBParticipants = $this->getDoctrine()->getManager()->
                createQuery($queryTextParticipants)->
                setParameters([
                    'inventoryDId' => $query->id, 
                ]);
            
        $toReturn = $result[0];
        
        $toReturn->participants = [];
        if (!empty($queryDBParticipants)) {
            foreach ($queryDBParticipants->getArrayResult() as $value) {
                $toReturn->participants[] = $value['participantId'];
            }
        }
        
        return $toReturn;
    }

}
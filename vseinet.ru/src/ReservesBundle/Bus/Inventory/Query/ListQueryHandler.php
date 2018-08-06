<?php 

namespace ReservesBundle\Bus\Inventory\Query;

use AppBundle\Bus\Message\MessageHandler;
use ReservesBundle\Bus\Inventory\Query\DTO\DocumentList;
//use ReservesBundle\Entity\Inventory;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {
        $setParameters = [];
        
        $currentUser = $this->get('user.identity')->getUser();
        $setParameters['currentUser'] = $currentUser->getId();
        
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\Inventory\Query\DTO\Document(
                    i.dId,
                    i.number,
                    i.createdAt,
                    i.createdBy,
                    concat(
                        case when pc.firstname is null  then '' else pc.firstname end,
                        case when pc.secondname is null then '' else concat(' ', pc.secondname) end,
                        case when pc.lastname is null   then '' else concat(' ', pc.lastname) end),
                    i.title,
                    i.geoRoomId,
                    concat(case when gp.name is null then '' else concat(gp.name, ', ') end, gr.name),
                    i.responsibleId,
                    concat(
                        case when pr.firstname is null  then '' else pr.firstname end,
                        case when pr.secondname is null then '' else concat(' ', pr.secondname) end,
                        case when pr.lastname is null   then '' else concat(' ', pr.lastname) end),
                    i.completedAt,
                    i.status,
                    case when :currentUser = i.createdBy then 'owner' else
                        case when :currentUser = i.responsibleId then 'responsible' else
                            case when :currentUser in (select ip.participantId from ReservesBundle:InventoryParticipant ip where ip.inventoryDId = i.dId) then 'participant' else '' end
                        end
                    end
                )
            FROM ReservesBundle:Inventory i
            LEFT JOIN AppBundle:User uc WITH i.createdBy = uc.id  
            LEFT JOIN AppBundle:Person pc WITH uc.personId = pc.id  
            LEFT JOIN AppBundle:User ur WITH i.responsibleId = ur.id  
            LEFT JOIN AppBundle:Person pr WITH ur.personId = pr.id  
            LEFT JOIN OrgBundle\Entity\GeoRoom gr WITH gr.id = i.geoRoomId
            LEFT JOIN OrgBundle\Entity\GeoPoint gp WITH gp.id = gr.geoPointId
            WHERE 1=1";

        // Интервал дат
        
        if ($query->fromDate) {
            $queryText .= "
                and i.createdAt >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate.'T00:00:00';
        }
        if ($query->toDate) {
            $queryText .= "
                and i.createdAt <= :toDate";
            $setParameters['toDate'] = $query->toDate.'T23:59:59';
        }
        
        // Просмотр архива
        
        if (!$query->withCompleted) {
            $queryText .= "
                and i.status != 'completed'";
        }

        $queryText .= "
            ORDER BY i.createdAt DESC, i.number DESC";

        $queryDB = $this->getDoctrine()->getManager()->createQuery($queryText);
        
        // Пагинация
        
        if ($query->limit) {
            $queryDB->
                    setMaxResults($query->limit);
        }
        if ($query->page) {
            $queryDB->
                    setFirstResult(($query->page - 1) * $query->limit);
        }
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
        
        // dirti counter
            
        $queryText = "
            SELECT
                NEW ReservesBundle\Bus\Inventory\Query\DTO\Counter(
                    count(1)
                )
            FROM ReservesBundle:Inventory i
            WHERE 1=1";

        // Интервал дат
        
        if ($query->fromDate) {
            $queryText .= "
                and i.createdAt >= :fromDate";
        }
        if ($query->toDate) {
            $queryText .= "
                and i.createdAt <= :toDate";
        }
        
        // Просмотр архива
        
        if (!$query->withCompleted) {
            $queryText .= "
                and i.status != 'completed'";
        }

        $countDB = $this->getDoctrine()->getManager()->createQuery($queryText);
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
        
        return new DocumentList($queryDB->getArrayResult(), $countDB->getArrayResult()[0]->total);
        
    }

}
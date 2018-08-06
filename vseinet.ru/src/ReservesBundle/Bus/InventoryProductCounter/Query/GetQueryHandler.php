<?php 

namespace ReservesBundle\Bus\InventoryProductCounter\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\InventoryProductCounter\Query\DTO\InventoryParticipantCounter(
                    ic.inventoryDId,
                    ic.participantId,
                    concat(
                        case when pp.firstname is null  then '' else concat(pp.firstname, ' ') end,
                        case when pp.secondname is null then '' else concat(pp.secondname, ' ') end,
                        case when pp.lastname is null   then '' else pp.lastname end),
                    ic.baseProductId,
                    bp.name,
                    ic.foundQuantity,
                    ic.comment
                )
            FROM ReservesBundle:InventoryProductCounter ic
            LEFT JOIN AppBundle:User            uc WITH ic.participantId = uc.id
            LEFT JOIN AppBundle:Person          pp WITH uc.personId = pp.id
            LEFT JOIN ContentBundle:BaseProduct bp WITH ic.baseProductId = bp.id
            WHERE ic.inventoryDId = :inventoryDId and
                ic.participantId = :participantId
            ORDER BY ic.baseProductId, ic.participantId";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameters([
                    'inventoryDId' => $query->inventoryId,
                    'participantId' => $this->get('user.identity')->getUser()
                ]);
            
        return $queryDB->getArrayResult();
        
    }

}
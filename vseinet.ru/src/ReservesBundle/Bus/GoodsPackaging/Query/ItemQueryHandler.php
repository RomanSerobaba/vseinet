<?php 

namespace ReservesBundle\Bus\GoodsPackaging\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsPackaging\Query\DTO\GoodsPackagingItem(
                    i.dId,
                    i.number,
                    i.title,
                    i.createdAt,
                    i.createdBy,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname, ' ') end,
                        case when pc.secondname is null then '' else concat(pc.secondname, ' ') end,
                        case when pc.lastname is null   then '' else concat(pc.lastname, ' ') end),
                    i.completedAt,
                    i.completedBy,
                    concat(
                        case when pa.firstname is null  then '' else concat(pa.firstname, ' ') end,
                        case when pa.secondname is null then '' else concat(pa.secondname, ' ') end,
                        case when pa.lastname is null   then '' else concat(pa.lastname, ' ') end),
                    i.geoRoomId,
                    concat(case when gp.name is null then '' else concat(gp.name, ', ') end, gr.name),
                    i.baseProductId,
                    bp.name,
                    i.quantity,
                    i.type
                )
            FROM ReservesBundle:GoodsPackaging i
            LEFT JOIN AppBundle:User uc WITH i.createdBy = uc.id  
            LEFT JOIN AppBundle:Person pc WITH uc.personId = pc.id  
            LEFT JOIN AppBundle:User ua WITH i.completedBy = ua.id  
            LEFT JOIN AppBundle:Person pa WITH ua.personId = pa.id  
            LEFT JOIN OrgBundle\Entity\GeoRoom gr WITH gr.id = i.geoRoomId
            LEFT JOIN OrgBundle\Entity\GeoPoint gp WITH gp.id = gr.geoPointId
            INNER JOIN ContentBundle\Entity\BaseProduct bp WITH i.baseProductId = bp.id
            WHERE i.dId = :dId";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameter('dId', $query->id);
        
        $result = $queryDB->getArrayResult();
        if (count($result) == 0) throw new NotFoundHttpException('Докмент не найден');
        
        return $result[0];
    }

}
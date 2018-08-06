<?php 

namespace ReservesBundle\Bus\GoodsPackaging\Query;

use AppBundle\Bus\Message\MessageHandler;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $limit = !empty($query->limit) ? intval($query->limit) : 50;

        $setParameters = [];

        $queryCount = "
            SELECT 
                COUNT(DISTINCT i.dId) AS cnt
            FROM ReservesBundle:GoodsPackaging i
                INNER JOIN ContentBundle\Entity\BaseProduct bp WITH i.baseProductId = bp.id
            WHERE 1=1";

        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsPackaging\Query\DTO\GoodsPackaging(
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
            WHERE 1=1";

        if ($query->baseProductId) {
            $queryCount .= "
                and i.baseProductId = :baseProductId";
            $queryText .= "
                and i.baseProductId = :baseProductId";
            $setParameters['baseProductId'] = $query->baseProductId;
        }
        
        // Интервал дат
        
        if ($query->fromDate) {
            $queryCount .= "
                and i.createdAt >= :fromDate";
            $queryText .= "
                and i.createdAt >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate.'T00:00:00';
        }
        if ($query->toDate) {
            $queryCount .= "
                and i.createdAt <= :toDate";
            $queryText .= "
                and i.createdAt <= :toDate";
            $setParameters['toDate'] = $query->toDate.'T23:59:59';
        }
        
        // Просмотр архива
        
        if (!$query->withCompleted) {
            $queryCount .= "
                and i.completedAt is null";
            $queryText .= "
                and i.completedAt is null";
        }

        $queryText .= "
            ORDER BY i.createdAt DESC, i.number DESC";

        /** @var \Doctrine\ORM\Query $queryDB */
        $queryDB = $em->createQuery($queryText);
        
        // Пагинация
        
        $queryDB->setMaxResults($limit);
        if (!empty($query->page)) {
            $queryDB->setFirstResult((max(intval($query->page), 1) - 1) * $limit);
        }
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);

        $items = $queryDB->getArrayResult();


        $queryDBCount = $em->createQuery($queryCount);

        if (count($setParameters) > 0) $queryDBCount->setParameters($setParameters);

        $total = $queryDBCount->getScalarResult();

        return ['items' => $items, 'total' => $total[0]['cnt']];
    }
}

<?php 
namespace ReservesBundle\Bus\GoodsPallet\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        
        $setParameters = [];

        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsPallet\Query\DTO\GoodsPallet(
                    gp.id,
                    gp.createdAt,
                    gp.createdBy,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname, ' ') end,
                        case when pc.secondname is null then '' else concat(pc.secondname, ' ') end,
                        case when pc.lastname is null   then '' else pc.lastname end),
                    gp.title,
                    gp.geoPointId,
                    concat(gep.name, ' (', gep.code, ')'),
                    gp.status
                )
            FROM ReservesBundle:GoodsPallet gp
            LEFT JOIN OrgBundle\Entity\GeoPoint gep WITH gp.geoPointId = gep.id
            LEFT JOIN AppBundle:User uc WITH gp.createdBy = uc.id  
            LEFT JOIN AppBundle:Person pc WITH uc.personId = pc.id  
            WHERE gp.status in (:statuses)";
        
        ////////////////////////////////////////////////////

        $statuses = [];
        
        if (!empty($query->withFree)) {
            $statuses[] = 'free';
        }
        if (!empty($query->withOpened)) {
            $statuses[] = 'opened';
        }
        if (!empty($query->withClosed)) {
            $statuses[] = 'closed';
        }
        if (!empty($query->withInWay)) {
            $statuses[] = 'in_way';
        }
        if (!empty($query->withWriteOff)) {
            $statuses[] = 'write_off';
        }
        
        $setParameters['statuses'] = &$statuses;
        
        ////////////////////////////////////////////////////
        //
        //  Интервал дат
        //
        
        if ($query->fromDate) {
            $queryText .= "
                and gp.createdAt >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate.'T00:00:00';
        }
        if ($query->toDate) {
        
            $queryText .= "
                and gp.createdAt <= :toDate";
            $setParameters['toDate'] = $query->toDate.'T23:59:59';
        }
        
        $queryText .= "
            ORDER BY gp.createdAt DESC, gp.id DESC";

        $queryDB = $this->getDoctrine()->getManager()->createQuery($queryText);
        
        ////////////////////////////////////////////////////
        //
        //  Пагинация
        //
        
        if ($query->limit) {
            $queryDB->
                    setMaxResults($query->limit);
        }
        if ($query->page) {
            $queryDB->
                    setFirstResult($query->page * $query->limit);
        }
        
        if (count($setParameters) > 0) $queryDB->setParameters($setParameters);
            
        return $queryDB->getArrayResult();

    }

}
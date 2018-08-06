<?php 

namespace ReservesBundle\Bus\GoodsPallet\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsPallet\Query\DTO\GoodsPalletItem(
                    gp.id,
                    gp.createdAt,
                    gp.createdBy,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname, ' ') end,
                        case when pc.secondname is null then '' else concat(pc.secondname, ' ') end,
                        case when pc.lastname is null   then '' else pc.lastname end),
                    gp.title,
                    gp.geoPointId,
                    gep.name||' ('||gep.code||')',
                    gp.status
                )
            FROM ReservesBundle:GoodsPallet gp
            LEFT JOIN OrgBundle::GeoPoint gep WITH gp.geoPointId = gep.id
            LEFT JOIN AppBundle:User uc WITH i.createdBy = uc.id  
            LEFT JOIN AppBundle:Person pc WITH uc.personId = pc.id  
            WHERE
                gp.id = :id";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameters([
                    'id' => $query->id
                ]);
        
        $result = $queryDB->getArrayResult();
        if (count($result) == 0) throw new NotFoundHttpException('Элемент не найден');
        
        return $result[0];
    }

}
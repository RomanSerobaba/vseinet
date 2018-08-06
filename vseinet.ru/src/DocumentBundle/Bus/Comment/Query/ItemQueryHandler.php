<?php 

namespace DocumentBundle\Bus\Comment\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $queryText = "
            SELECT 
                NEW DocumentBundle\Bus\Comment\Query\DTO\Comment(
                    i.id,
                    i.documentId,
                    i.createdAt,
                    i.createdBy,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname,
                            case when pc.lastname is null and pc.secondname is null then '' else ' ' end) end,
                        case when pc.secondname is null then '' else concat(pc.secondname,
                            case when pc.lastname is null   then '' else ' ' end) end,
                        case when pc.lastname is null   then '' else pc.lastname end),
                    i.comment
                )
            FROM DocumentBundle:Comment i
            
            LEFT JOIN AppBundle:User uc WITH i.createdBy = uc.id  
            LEFT JOIN AppBundle:Person pc WITH uc.personId = pc.id
            
            WHERE i.id = :id
            ";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameter('id', $query->id);
        
        $result = $queryDB->getArrayResult();
        if (count($result) == 0) throw new NotFoundHttpException('Документ не найден');
        
        return $result[0];
    }

}

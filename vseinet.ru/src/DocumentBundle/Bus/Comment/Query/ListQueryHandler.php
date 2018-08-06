<?php 

namespace DocumentBundle\Bus\Comment\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
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
            
            where i.documentId = :documentId
            
            order by i.createdAt
            ";

        $queryDB = $this->getDoctrine()->getManager()->createQuery($queryText)->setParameters([
            'documentId' => $query->documentId,
        ]);
            
        return $queryDB->getArrayResult();
    }

}
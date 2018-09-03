<?php 

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetDetailQueryHandler extends MessageHandler
{
    public function handle(GetDetailQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Catalog\Query\DTO\Detail (
                    d.id, 
                    d.name,
                    dv.id,
                    dv.value 
                )
            FROM AppBundle:DetailToProduct AS d2p 
            INNER JOIN AppBundle:Detail AS d WITH d.id = d2p.detailId
            INNER JOIN AppBundle:DetailValue AS dv WITH dv.id = d2p.valueId  
            WHERE dv.id = :id 
            GROUP BY d.id, dv.id
        ");
        $q->setParameter('id', $query->id);
        $detail = $q->getOneOrNullResult();

        if (!$detail instanceof DTO\Detail) {
            throw new NotFoundHttpException();
        }

        return $detail;
    }
}

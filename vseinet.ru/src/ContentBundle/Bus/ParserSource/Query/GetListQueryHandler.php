<?php 

namespace ContentBundle\Bus\ParserSource\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\ParserSource;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $where = "";
        if ('active' === $query->filter) {
            $where = "WHERE ps.isActive = true";
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ParserSource\Query\DTO\Source (
                    ps.id,
                    ps.supplierId,
                    ps.code,
                    ps.alias,
                    ps.url,
                    ps.useAntiGuard,
                    ps.isActive,
                    ps.isParseImages
                )
            FROM ContentBundle:ParserSource ps 
            {$where} 
            ORDER BY ps.code, ps.alias 
        ");

        return $q->getArrayResult();
    }
}
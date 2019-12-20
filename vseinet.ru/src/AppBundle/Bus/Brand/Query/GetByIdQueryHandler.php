<?php

namespace AppBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetByIdQueryHandler extends MessageHandler
{
    public function handle(GetByIdQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Brand\Query\DTO\Brand (
                    b.id,
                    b.name,
                    b.url,
                    b.isForbidden,
                    b.chpuName
                )
            FROM AppBundle:Brand AS b
            WHERE b.id = :id
        ");
        $q->setParameter('id', $query->id);
        $brand = $q->getOneOrNullResult();
        if (!$brand instanceof DTO\Brand || ($brand->isForbidden && !$this->getUserIsEmployee())) {
            throw new NotFoundHttpException();
        }

        return $brand;
    }
}

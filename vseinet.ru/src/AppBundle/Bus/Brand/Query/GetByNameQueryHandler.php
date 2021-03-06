<?php

namespace AppBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetByNameQueryHandler extends MessageHandler
{
    public function handle(GetByNameQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Brand\Query\DTO\Brand (
                    b.canonicalId,
                    b.name,
                    b.url,
                    b.isForbidden
                )
            FROM AppBundle:Brand AS b
            WHERE LOWER(b.name) = LOWER(:name)
        ");
        $q->setParameter('name', $query->name);
        $brand = $q->getOneOrNullResult();

        if (!$brand instanceof DTO\Brand) {
            throw new NotFoundHttpException();
        }
        if ($brand->isForbidden && !$this->getUserIsEmployee()) {
            throw new NotFoundHttpException();
        }

        return $brand;
    }
}

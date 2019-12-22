<?php

namespace AppBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetBySefNameQueryHandler extends MessageHandler
{
    public function handle(GetBySefNameQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Brand\Query\DTO\Brand (
                    b.canonicalId,
                    b.name,
                    b.url,
                    b.isForbidden,
                    b.sefName
                )
            FROM AppBundle:Brand AS b
            WHERE b.sefName = :sefName AND b.id = b.canonicalId
        ");
        $q->setParameter('sefName', $query->sefName);
        $brand = $q->getOneOrNullResult();

        if ($brand->isForbidden && !$this->getUserIsEmployee()) {
            throw new NotFoundHttpException();
        }

        return $brand;
    }
}

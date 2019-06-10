<?php

namespace AppBundle\Bus\Pricetags\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Pricetag;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\GeoPoint;

class ToggleCommandHandler extends MessageHandler
{
    public function handle(ToggleCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricetag = $em->getRepository(Pricetag::class)->findOneBy([
            'baseProductId' => $command->baseProductId,
            'geoPointId' => $command->geoPointId,
        ]);

        if ($pricetag instanceof Pricetag) {
            $em->remove($pricetag);
            $em->flush();

            return false;
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $geoPoint = $em->getRepository(GeoPoint::class)->find($command->geoPointId);
        if (!$geoPoint instanceof GeoPoint) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->geoPointId));
        }

        $q = $em->createQuery('
            SELECT COALESCE(p.price, p0.price) AS price
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:Product AS p0 WITH p0.baseProductId = bp.id AND p0.geoCityId = 0
            LEFT OUTER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            WHERE bp.id = :baseProductId
        ');
        $q->setParameter('baseProductId', $baseProduct->getId());
        $q->setParameter('geoCityId', $geoPoint->getGeoCityId());
        $price = $q->getSingleScalarResult();

        $pricetag = new Pricetag();
        $pricetag->setBaseProductId($baseProduct->getId());
        $pricetag->setGeoPointId($geoPoint->getId());
        $pricetag->setPrice($price);

        $em->persist($pricetag);
        $em->flush($pricetag);

        return true;
    }
}

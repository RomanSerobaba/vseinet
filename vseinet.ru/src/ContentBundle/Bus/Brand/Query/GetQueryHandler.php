<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Bus\Brand\Query\DTO\Brand;
use ContentBundle\Entity\BrandPseudo;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query) 
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW ContentBundle\Bus\Brand\Query\DTO\Brand (
                    b.id,
                    b.name,
                    b.logo,
                    b.url,
                    b.isForbidden,
                    COUNT(bp.id) 
                )
            FROM ContentBundle:Brand b 
            LEFT OUTER JOIN ContentBundle:BaseProduct bp WITH bp.brandId = b.id
            WHERE b.id = :id 
            GROUP BY b.id
        ");
        $q->setParameter('id', $query->id);

        $brand = $q->getSingleResult();
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %d не найден', $query->id));
        }

        $brand->pseudos = $em->getRepository(BrandPseudo::class)->findBy(['brandId' => $brand->id], ['name' => 'ASC']);

        return $brand;
    }
}
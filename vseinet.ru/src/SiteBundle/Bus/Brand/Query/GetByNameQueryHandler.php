<?php 

namespace SiteBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetByNameQueryHandler extends MessageHandler
{
    public function handle(GetByNameQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        
        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Brand\Query\DTO\Brand (
                    b.id, 
                    b.name,
                    b.url,
                    b.isForbidden
                )
            FROM ContentBundle:Brand AS b 
            WHERE LOWER(b.name) = LOWER(:name)
        ");
        $q->setParameter('name', $query->name);
        $brand = $q->getOneOrNullResult();
        if (!$brand instanceof DTO\Brand) {
            $q = $em->createQuery("
                SELECT 
                    SiteBundle\Bus\Brand\Query\DTO\Brand (
                        b.id,
                        bp.name,
                        b.url,
                        b.isForbidden
                    )
                FROM ContentBundle:Brand AS b 
                INNER JOIN ContentBundle:BrandPseudo AS bp WITH bp.brandId = b.id 
                WHERE LOWER(bp.name) = LOWER(:name)
            ");
            $q->setParameter('name', $query->name);
            $brand = $q->getOneOrNullResult();
            if (!$brand instanceof DTO\Brand) {
                throw new NotFoundHttpException();   
            }
        }
        if ($brand->isForbidden && !$this->get('user.identity')->isEmployee()) {
            throw new NotFoundHttpException();
        }

        return $brand;
    }
}

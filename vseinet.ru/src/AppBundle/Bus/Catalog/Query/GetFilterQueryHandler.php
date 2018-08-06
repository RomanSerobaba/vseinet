<?php 

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetFilterQueryHandler extends MessageHandler
{
    public function handle(GetFilterQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\Catalog\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.aliasForId,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id OR cc.pid = c.aliasForId
                    ) THEN false ELSE true END,
                    co.description,
                    co.pageTitle,
                    co.pageDescription,
                    c.isTplEnabled
                )
            FROM ContentBundle:Category c
            WHERE c.id = :id 
        ");
        $q->setParameter('id', $query->id);
        $category = $q->getOneOrNullResult();

        if (!$category instanceof DTO\Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->id)); 
        }

        if ($query->brand) {
            $brand = $em->getRepository(Brand::class)->findOneBy(['name' => $query->brand]);
            if (!$brand instanceof Brand) {
                throw new NotFoundHttpException(sprintf('Бренд %s не найден', $query->brand));
            }
            
        }
    }
}
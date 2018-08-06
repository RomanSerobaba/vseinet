<?php 

namespace ContentBundle\Bus\SupplierCategory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierCategory;

class SetIsHiddenCommandHandler extends MessageHandler
{
    public function handle(SetIsHiddenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(SupplierCategory::class)->find($command->id);
        if (!$category instanceof SupplierCategory) {
            throw new NotFoundHttpException(sprintf('Категория поставщика %d не найдена', $command->id));
        }

        $q = $em->createQuery("
            UPDATE SupplyBundle:SupplierCategory sc 
            SET sc.isHidden = :isHidden 
            WHERE sc.id IN (
                SELECT scp.id 
                FROM SupplyBundle:SupplierCategoryPath scp 
                WHERE scp.pid = :categoryId
            )
        ");
        $q->setParameter('isHidden', $command->isHidden);
        $q->setParameter('categoryId', $category->getId());
        $q->execute();

        if (false === $command->isHidden) {
            $q = $em->createQuery("
                UPDATE SupplyBundle:SupplierCategory sc 
                SET sc.isHidden = false 
                WHERE sc.id IN (
                    SELECT scp.pid 
                    FROM SupplyBundle:SupplierCategoryPath scp 
                    WHERE scp.id = :categoryId
                ) 
            ");
            $q->setParameter('categoryId', $category->getId());
            $q->execute();
        }

        $em->flush();
    }
}

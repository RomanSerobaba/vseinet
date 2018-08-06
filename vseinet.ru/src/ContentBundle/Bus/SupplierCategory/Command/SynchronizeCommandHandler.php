<?php 

namespace ContentBundle\Bus\SupplierCategory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierCategory;
use ContentBundle\Entity\Category;

class SynchronizeCommandHandler extends MessageHandler
{
    public function handle(SynchronizeCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplierCategory = $em->getRepository(SupplierCategory::class)->find($command->id);
        if (!$supplierCategory instanceof SupplierCategory) {
            throw new NotFoundHttpException(sprintf('Категория поставщика %d не найдена', $command->id));
        }
        
        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->categoryId));
        }

        $supplierCategory->setSyncCategoryId($category->getId());
        
        $em->persist($supplierCategory);
        $em->flush();
    }
}

<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OrgBundle\Entity\Representative;
use MatrixBundle\Entity\TradeMatrixCategoryToRepresentative;
use ContentBundle\Entity\Category;

class UpdateCategoryQuantityCommandHandler extends MessageHandler
{
    public function handle(UpdateCategoryQuantityCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $representative = $em->getRepository(Representative::class)->find($command->id);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->id));
        }

        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->categoryId));
        }

        $representativeCategory = $em->getRepository(TradeMatrixCategoryToRepresentative::class)->findOneBy(['representativeId' => $command->id, 'categoryId' => $command->categoryId]);

        if (!$representativeCategory instanceof TradeMatrixCategoryToRepresentative) {
            $representativeCategory = new TradeMatrixCategoryToRepresentative();
            $representativeCategory->setRepresentativeId($command->id);
            $representativeCategory->setCategoryId($command->categoryId);
        }

        $representativeCategory->setQuantity($command->quantity);
        $em->persist($representativeCategory);
    }
}
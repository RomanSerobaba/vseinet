<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\BaseProduct;

class MoveCommandHandler extends MessageHandler
{
    public function handle(MoveCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->categoryId));
        }

        $products = $em->getRepository(BaseProduct::class)->findBY(['id' => $command->ids]);
        if (empty($products)) {
            throw new BadRequestHttpException('Выберите товары для перемещения');
        }

        foreach ($products as $product) {
            $product->setCategoryId($category->getId());
            $em->persist($product);
        }

        $em->flush();
    }
}
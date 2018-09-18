<?php 

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Category;

class MoveCommandHandler extends MessageHandler
{
    public function handle(MoveCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        $category = $em->getRepository(Category::class)->find($command->category->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->category->id));
        }
        if ($category->getId() === $product->getCategoryId()) {
            throw new BadRequestHttpException(sprintf('Товар с кодом %d у же в категории %d', $command->id, $command->category->id));
        }
        $child = $em->getRepository(Category::class)->findOneBy(['pid' => $category->getId()]);
        if ($child instanceof Category) {
            throw new BadRequestHttpException('Товары можно перемещать только в конечные категории');
        }

        $product->setCategoryId($category->getId());
        $em->persist($product);
        $em->flush();
    }
}

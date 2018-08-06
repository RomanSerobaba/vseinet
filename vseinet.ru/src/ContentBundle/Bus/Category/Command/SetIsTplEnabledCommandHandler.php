<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class SetIsTplEnabledCommandHandler extends MessageHandler
{
    public function handle(SetIsTplEnabledCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->id));
        }

        $category->setIsTplEnabled($command->isTplEnabled);

        $em->persist($category);
        $em->flush();
    }
}
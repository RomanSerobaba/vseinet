<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\DetailGroup;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->categoryId));
        }

        $group = new DetailGroup();
        $group->setCategoryId($category->getId());
        $group->setName($command->name);

        $em->persist($group);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $group->getId());
    }
}
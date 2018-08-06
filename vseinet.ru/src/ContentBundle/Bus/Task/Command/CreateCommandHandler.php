<?php 

namespace ContentBundle\Bus\Task\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Manager;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\Task;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $manager = $em->getRepository(Manager::class)->find($command->managerId);
        if (!$manager instanceof Manager) {
            throw new NotFoundHttpException(sprintf('Контент-манажер %s не найден', $command->managerId));
        }

        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %s  не найдена', $command->categoryId));
        }

        $task = new Task();
        $task->setManagerId($manager->getUserId());
        $task->setCategoryId($category->getId());

        $em->persist($task);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $task->getId());
    }
}

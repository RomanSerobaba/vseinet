<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Category;

class MoveCommandHandler extends MessageHandler 
{
    public function handle(MoveCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->id));
        }

        if ($category->getPid() == $command->pid) {
            throw new BadRequestHttpException('Категория уже находится в этом месте');
        }; 
          
        $parent = $em->getRepository(Category::class)->find($command->pid);
        if (!$parent instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->pid));
        }

        $category->setPid($parent->getId());

        $em->persist($category);
        $em->flush();

        $em->getRepository(Category::class)->updatePaths($category->getId(), $category->getPid());

        // bridge to old site
        $this->get('old_sound_rabbit_mq.execute.script_producer')->publish(json_encode([
            'url' => '/admin/bridge/category/move',
            'args' => $command->toArray(), 
        ]));
    }
}
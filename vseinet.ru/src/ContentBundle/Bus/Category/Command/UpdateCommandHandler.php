<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->id));
        }

        if ($command->aliasForId) {
            $linkedCategory = $em->getRepository(Category::class)->find($command->aliasForId);
            if (!$linkedCategory instanceof Category) {
                throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->aliasForId));
            }
        }
        else {
            $linkedCategory = new Category();
        }

        $category->setName($command->name);
        $category->setBasename($command->basename);
        $category->setAliasForId($linkedCategory->getId());
        $category->setGender($command->gender);
        
        $em->persist($category);
        $em->flush();

        // bridge to old site
        $this->get('old_sound_rabbit_mq.execute.script_producer')->publish(json_encode([
            'url' => '/admin/bridge/category/update',
            'args' => json_decode($this->serialize($category), true), 
        ]));
    }
}
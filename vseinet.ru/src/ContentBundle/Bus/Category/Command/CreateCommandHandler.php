<?php

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use AppBundle\Enum\CategoryTpl;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $parent = $em->getRepository(Category::class)->find($command->pid);
        if (!$parent instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->pid));
        }

        if ($command->aliasForId) {
            $linkedCategory = $em->getRepository(Category::class)->find($command->aliasForId);
            if (!$linkedCategory instanceof Category) {
                throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->aliasForId));
            }
        }

        $category = new Category();
        $category->setName($command->name);
        $category->setPid($command->pid);
        $category->setAliasForId($command->aliasForId);
        $category->setBasename($command->basename);
        $category->setGender($command->gender);
        $category->setTpl(CategoryTpl::NONE);
        $category->setIsTplEnabled(false);
        $category->SetUseExname(false);

        $em->persist($category);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $category->getId());

        // bridge to old site
        $this->get('old_sound_rabbit_mq.execute.script_producer')->publish(json_encode([
            'url' => '/admin/bridge/category/create',
            'args' => json_decode($this->serialize($category), true), 
        ]));
    }
}
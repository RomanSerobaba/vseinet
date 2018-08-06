<?php

namespace ContentBundle\Bus\CategorySection\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\CategorySection;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория с кодом %d не найдена', $command->categoryId));
        }

        if (null !== $category->getAliasForId()) {
            $command->categoryId = $category->getAliasForId();
        }

        if (null === $command->gender) {
            $command->gender = $category->getGender();
        }

        $section = new CategorySection();
        $section->setCategoryId($command->categoryId);
        $section->setName($command->name);
        $section->setBasename($command->basename);
        $section->setGender($command->gender);

        $em->persist($section);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $section->getId());
    }
}
<?php

namespace ContentBundle\Bus\CategorySection\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\CategorySection;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository(CategorySection::class)->find($command->id);
        if (!$section instanceof CategorySection) {
            throw new NotFoundHttpException(sprintf('Раздел категории с кодом %d не найден', $command->id));    
        }

        $section->setName($command->name);
        $section->setBasename($command->basename);
        $section->setGender($command->gender);

        $em->persist($section);
        $em->flush();
    }
}
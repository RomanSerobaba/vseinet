<?php

namespace ContentBundle\Bus\CategorySection\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\CategorySection;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository(CategorySection::class)->find($command->id);
        if (!$section instanceof CategorySection) {
            throw new NotFoundHttpException(sprintf('Раздел категории с кодом %d не найден', $command->id));    
        }

        $em->remove($section);
        $em->flush();
    }
}
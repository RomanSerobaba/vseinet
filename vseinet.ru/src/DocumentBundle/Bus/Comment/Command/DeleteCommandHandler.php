<?php

namespace DocumentBundle\Bus\Comment\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DocumentBundle\Entity\Comment;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(Comment::class)->find($command->id);
        if (!$item instanceof Comment) {
            throw new NotFoundHttpException('Коментарий документа не найден');
        }
        
        $em->remove($item);
        $em->flush();
        
    }
}
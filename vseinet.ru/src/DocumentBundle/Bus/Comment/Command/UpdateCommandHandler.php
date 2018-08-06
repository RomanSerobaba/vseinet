<?php

namespace DocumentBundle\Bus\Comment\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use DocumentBundle\Entity\Comment;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(Comment::class)->find($command->id);
        if (!$item instanceof Comment) {
            throw new NotFoundHttpException('Комментарий не найден (команда)');
        }

        $currentUser = $this->get('user.identity')->getUser();        
        
        $item->setCreatedBy($currentUser->getId());
        $item->setComment($command->comment);
    
        $em->persist($item);
        $em->flush();

    }
    
}

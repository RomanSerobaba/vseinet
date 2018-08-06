<?php

namespace DocumentBundle\Bus\Comment\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DocumentBundle\Entity\Comment;
use DocumentBundle\Entity\GoodsIssueDoc;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
//        $document = $em->getRepository(GoodsIssueDoc::class)->find($command->documentId);
//        if (!$document instanceof GoodsIssueDoc) {
//            throw new NotFoundHttpException('Документ не найден');
//        }
        
        $currentUser = $this->get('user.identity')->getUser();

        $item = new Comment();
        
        $item->setCreatedBy($currentUser->getId());
        $item->setDocumentId($command->documentId);
        $item->setComment($command->comment);

        $em->persist($item);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $item->getId());

    }
    
}

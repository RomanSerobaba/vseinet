<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Entity\GoodsIssueDocProduct;

use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocUnRegistration;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsIssueDoc::class)->find($command->id);
        if (!$document instanceof GoodsIssueDoc) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        if (!empty($document->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно (команда)');
        }

        GoodIssueDocUnRegistration::unRegistration($document, $em, $this->get('user.identity')->getUser());
        
        $documentProducts = $em->getRepository(GoodsIssueDocProduct::class)->findBy(['goodsIssueDocId' => $command->id]);
        foreach ($documentProducts as $documentProduct) {
            $em->remove($documentProduct);
        }
        $em->remove($document);
        $em->flush();
        
    }
}
<?php

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsPackaging;

class CompletedCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(CompletedCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(GoodsPackaging::class)->find($command->id);
        if (!$item instanceof GoodsPackaging) {
            throw new NotFoundHttpException('Документ комплектации/разкомплектации не найден');
        }
        
        if (!empty($command->completed)) {
            
            $item->setCompletedAt(new \DateTime);
            $item->setCompletedBy($this->get('user.identity')->getUser()->getId());
            
        }
        else{
            
            $item->setCompletedAt();
            $item->setCompletedBy();
            
        }
        
        $em->persist($item);
        $em->flush();
        
    }
    
}

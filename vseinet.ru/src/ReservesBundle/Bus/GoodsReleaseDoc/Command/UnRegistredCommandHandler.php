<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocUnRegistration;

class UnRegistredCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UnRegistredCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsRelease::class)->find($command->id);
        if (!$document instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        GoodsReleaseDocUnRegistration::UnRegistration($document, $em, $this->get('user.identity')->getUser());

    }
    
}

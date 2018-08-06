<?php

namespace ReservesBundle\Bus\GoodsPallet\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsPallet;

class DeleteCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(DeleteCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository(GoodsPallet::class)->find($command->id);
        if (!$item instanceof GoodsPallet) {
            throw new NotFoundHttpException('Паллета не найдена');
        }
        
        if (\AppBundle\Enum\GoodsPalletStatusType::FREE != $item->getStatus()) {
            throw new ConflictHttpException('Палета уже используется и не может быть удалена.');
        }
        
        $em->remove($item);
        $em->flush();

        return;
    }

}

<?php 

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Color;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $color = $em->getRepository(Color::class)->find($command->id);
        if (!$color instanceof Color) {
            throw new NotFoundHttpException('Цвет не найден');
        }

        $em->remove($color);
        $em->flush();
    }
}
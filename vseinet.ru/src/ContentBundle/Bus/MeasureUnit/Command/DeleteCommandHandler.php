<?php 

namespace ContentBundle\Bus\MeasureUnit\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\MeasureUnit;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $unit = $em->getRepository(MeasureUnit::class)->find($command->id);
        if (!$unit instanceof MeasureUnit) {
            throw new NotFoundHttpException(sprintf('Единица измерения %d не найдена', $command->id));
        }

        $em->remove($unit);
        $em->flush();
    }
}
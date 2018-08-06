<?php 

namespace ContentBundle\Bus\Measure\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Measure;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $measure = $em->getRepository(Measure::class)->find($command->id);
        if (!$measure instanceof Measure) {
            throw new NotFoundHttpException(sprintf('Группа физических величин %d не найдена', $command->id));
        }

        $measure->setName($command->name);

        $em->persist($measure);
        $em->flush();
    }
}
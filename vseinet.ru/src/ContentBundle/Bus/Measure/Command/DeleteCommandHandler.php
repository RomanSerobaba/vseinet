<?php 

namespace ContentBundle\Bus\Measure\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Measure;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $measure = $em->getRepository(Measure::class)->find($command->id);
        if (!$measure instanceof Measure) {
            throw new NotFoundHttpException(sprintf('Группа физических величин %d не найдена', $command->id));
        }

        $q = $em->createQuery("
            SELECT 1
            FROM ContentBundle:MeasureUnit u 
            INNER JOIN ContentBundle:Detail d WITH d.unitId = u.id  
            WHERE u.measureId = :measureId 
        ");
        $q->setParameter('measureId', $measure->getId());
        if ($q->getOneOrNullResult()) {
            throw new BadRequestHttpException('Нельзя удалить не пустую группу физических величин');
        }

        $em->remove($measure);
        $em->flush();
    }
}
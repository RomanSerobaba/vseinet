<?php 

namespace ContentBundle\Bus\MeasureUnit\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Measure;
use ContentBundle\Entity\MeasureUnit;
use ContentBundle\Entity\MeasureUnitAlias;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $measure = $em->getRepository(Measure::class)->find($command->measureId);
        if (!$measure instanceof Measure) {
            throw new NotFoundHttpException(sprintf('Группа физических величин %d не найдена', $command->measureId));
        }

        $unit = new MeasureUnit();
        $unit->setMeasureId($measure->getId());
        $unit->setName($command->name);
        $unit->setK($command->k);
        $unit->setUseSpace($command->useSpace);

        $em->persist($unit);
        $em->flush();

        if ($command->aliases) {
            $nameAliases = array_unique(array_filter(array_map('trim', $command->aliases)));
            foreach ($nameAliases as $nameAlias) {
                $alias = new MeasureUnitAlias();
                $alias->setUnitId($unit->getId());
                $alias->setName($nameAlias);
                $em->persist($alias);
            }
            $em->flush();
        }

        $this->get('uuid.manager')->saveId($command->uuid, $unit->getId());
    }
}
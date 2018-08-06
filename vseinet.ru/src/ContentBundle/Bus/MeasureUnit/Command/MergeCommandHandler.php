<?php 

namespace ContentBundle\Bus\MeasureUnit\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\MeasureUnit;

class MergeCommandHandler extends MessageHandler
{
    public function handle(MergeCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $unit = $em->getRepository(MeasureUnit::class)->find($command->id);
        if (!$unit instanceof MeasureUnit) {
            throw new NotFoundHttpException(sprintf('Единица измерения %d не найдена', $command->id));
        }

        $pool = $em->getRepository(MeasureUnit::class)->findBy(['id' => $command->poolIds]);
        if (empty($pool)) {
            throw new BadRequestHttpException('Выберите единицы измерения для объединения');
        }

        foreach ($pool as $source) {
            if ($unit === $source) {
                throw new BadRequestHttpException('Нельзя объединить еденицу измерения саму с собой');
            }
            if ($unit->getMeasureId() != $source->getMeasureId()) {
                throw new BadRequestHttpException('Объединять единицы измерения можно только в пределах одной группы');
            }
        }

        foreach ($pool as $source) {
            $q = $em->getConnection()->prepare("
                DELETE FROM content_measure_unit_alias sa 
                WHERE sa.content_measure_unit_id = :source_id AND EXISTS(
                    SELECT 1 
                    FROM content_measure_unit_alias a
                    WHERE a.content_measure_unit_id = :unit_id AND LOWER(a.name) = LOWER(sa.name)
                )
            ");
            $q->execute(['unit_id' => $unit->getId(), 'source_id' => $source->getId()]);

            $q = $em->getConnection()->prepare("
                UPDATE content_measure_unit_alias 
                SET content_measure_unit_id = :unit_id
                WHERE content_measure_unit_id = :source_id 
            ");
            $q->execute(['unit_id' => $unit->getId(), 'source_id' => $source->getId()]);

            $q = $em->getConnection()->prepare("
                INSERT INTO content_measure_unit_alias (content_measure_unit_id, name)
                VALUES (:unit_id, :name)
                ON CONFLICT (content_measure_unit_id, name) DO NOTHING
            ");
            $q->execute(['unit_id' => $unit->getId(), 'name' => $source->getName()]);  

            $q = $em->getConnection()->prepare("
                DELETE FROM content_measure_unit_alias
                WHERE content_measure_unit_id = :source_id
            ");
            $q->execute(['source_id' => $source->getId()]);

            $em->remove($source);
        }
        $em->flush();
    }
}
<?php 

namespace ContentBundle\Bus\MeasureUnit\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\MeasureUnit;
use ContentBundle\Entity\MeasureUnitAlias;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $unit = $em->getRepository(MeasureUnit::class)->find($command->id);
        if (!$unit instanceof MeasureUnit) {
            throw new NotFoundHttpException(sprintf('Единица измерения %d не найдена', $command->id));
        }

        $unit->setName($command->name);
        $unit->setK($command->k);
        $unit->setUseSpace($command->useSpace);
        $em->persist($unit);

        $aliases = $em->getRepository(MeasureUnitAlias::class)->findBy(['unitId' => $unit->getId()]);
        if ($command->aliases) {
            $nameAliases = array_unique(array_filter(array_map('trim', $command->aliases)));
            foreach ($aliases as $alias) {
                foreach ($nameAliases as $nameAlias) {
                    if ($alias->getName() == $nameAlias) {
                        continue 2;
                    }
                }
                $em->remove($alias);
            }
            foreach ($nameAliases as $nameAlias) {
                foreach ($aliases as $alias) {
                    if ($alias->getName() == $nameAlias) {
                        continue 2;
                    }
                }
                $newAlias = new MeasureUnitAlias();
                $newAlias->setUnitId($unit->getId());
                $newAlias->setName($nameAlias);
                $em->persist($newAlias);
            }
        }
        else {
            foreach ($aliases as $alias) {
                $em->remove($alias);
            }
        }

        $em->flush();
    }
}
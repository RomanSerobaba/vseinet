<?php 

namespace ContentBundle\Bus\Brand\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Brand;

class MergeCommandHandler extends MessageHandler
{
    public function handle(MergeCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %d не найден', $comman->id));
        }

        $pool = $em->getRepository(Brand::class)->findBy(['id' => $command->poolIds]);
        if (empty($pool)) {
            throw new BadRequestHttpException('Выберите бренды для объединения');
        }

        foreach ($pool as $source) {
            $q = $em->getConnection()->prepare("
                DELETE FROM brand_pseudo bp 
                WHERE bp.brand_id = :id AND EXISTS(
                    SELECT 1 
                    FROM brand_pseudo bp2 
                    WHERE bp2.brand_id = :source_id AND LOWER(bp2.name) = LOWER(bp.name)
                )
            ");
            $q->execute(['id' => $brand->getId(), 'source_id' => $source->getId()]);

            $q = $em->getConnection()->prepare("
                UPDATE brand_pseudo 
                SET brand_id = :brand_id
                WHERE brand_id = :source_id 
            ");
            $q->execute(['brand_id' => $brand->getId(), 'source_id' => $source->getId()]);

            $q = $em->getConnection()->prepare("
                INSERT INTO brand_pseudo (brand_id, name)
                VALUES (:brand_id, :name)
                ON CONFLICT (brand_id, name) DO NOTHING
            ");
            $q->execute(['brand_id' => $brand->getId(), 'name' => $source->getName()]);

            $q = $em->getConnection()->prepare("
                UPDATE base_product 
                SET brand_id = :brand_id 
                WHERE brand_id = :source_id 
            ");
            $q->execute(['brand_id' => $brand->getId(), 'source_id' => $source->getId()]);

            $em->remove($source);
        }
        $em->flush();
    }
}
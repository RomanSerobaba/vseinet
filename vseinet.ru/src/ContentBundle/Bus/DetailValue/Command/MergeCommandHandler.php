<?php 

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailValue;

class MergeCommandHandler extends MessageHandler
{
    public function handle(MergeCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        if ($command->id === $command->mergeId) {
            throw new BadRequestHttpException(sprintf('Нельзя объединить значение с самим собой'));
        }
        
        $value = $em->getRepository(DetailValue::class)->find($command->id);
        if (!$value instanceof DetailValue) {
            throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $command->id));
        }

        $merge = $em->getRepository(DetailValue::class)->find($command->mergeId);
        if (!$merge instanceof DetailValue) {
            throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $command->mergeId));
        }

        $q = $em->getConnection()->prepare("
            DELETE FROM content_detail_value_alias ma 
            WHERE ma.content_detail_value_id = :merge_id AND EXISTS(
                SELECT 1 
                FROM content_detail_value_alias a
                WHERE a.content_detail_value_id = :value_id AND LOWER(a.value) = LOWER(ma.value)
            )
        ");
        $q->execute(['value_id' => $value->getId(), 'merge_id' => $merge->getId()]);

        $q = $em->getConnection()->prepare("
            UPDATE content_detail_value_alias 
            SET content_detail_value_id = :value_id
            WHERE content_detail_value_id = :merge_id 
        ");
        $q->execute(['value_id' => $value->getId(), 'merge_id' => $merge->getId()]);

        $q = $em->getConnection()->prepare("
            INSERT INTO content_detail_value_alias (content_detail_value_id, value)
            VALUES (:value_id, :value)
            ON CONFLICT (content_detail_value_id, value) DO NOTHING
        ");
        $q->execute(['value_id' => $value->getId(), 'value' => $merge->getValue()]);  

        $q = $em->getConnection()->prepare("
            UPDATE content_detail_to_product 
            SET content_detail_value_id = :value_id 
            WHERE content_detail_id = :detail_id AND content_detail_value_id = :merge_id
        ");
        $q->execute(['value_id' => $value->getId(), 'detail_id' => $value->getDetailId(), 'merge_id' => $merge->getId()]);

        $em->remove($merge);
        $em->flush();
    }
}
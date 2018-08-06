<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadReqeustHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductMergeLog;
Use SupplyBundle\Entity\SupplyItem;
use OrderBundle\Entity\OrderItem;

class MergeCommandHandler extends MessageHandler
{
    public function handle(MergeCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }
        
        $pool = $em->getRepository(BaseProduct::class)->findBy(['id' => $command->mergeIds]);
        if (empty($pool)) {
            throw new BadReqeustHttpException('Выберите товары для объединения');
        }

        $pool = array_filter($pool, function($source) use ($product) {
            return $source->getId() != $product->getId();
        });
        if (empty($pool)) {
            throw new BadReqeustHttpException('Не возможно объединить товар с самим собой');
        }

        $poolIds = array_map(function($source) {
            return $source->getId();
        }, $pool);

        $q = $em->createQuery("
            UPDATE SupplyBundle:SupplyItem si 
            SET si.baseProductId = :id 
            WHERE si.baseProductId IN (:poolIds)
        ");
        $q->setParameter('id', $product->getId());
        $q->setParameter('poolIds', $poolIds);
        $q->execute();

        $q = $em->createQuery("
            UPDATE OrderBundle:OrderItem oi 
            SET oi.baseProductId = :id 
            WHERE oi.baseProductId IN (:poolIds)
        ");
        $q->setParameter('id', $product->getId());
        $q->setParameter('poolIds', $poolIds);
        $q->execute();

        $q = $em->createQuery("
            UPDATE ContentBundle:BaseProductMergeLog bpml 
            SET bpml.newId = :id
            WHERE bpml.oldId IN (:poolIds)
        ");
        $q->setParameter('id', $product->getId());
        $q->setParameter('poolIds', $poolIds);
        $q->execute();

        foreach ($pool as $source) {
            $log = new BaseProductMergeLog();
            $log->setNewId($product->getId());
            $log->setOldId($source->getId());
            $em->persist($log);
        }

        $q = $em->createQuery("
            DELETE FROM ContentBundle:BaseProduct bp 
            WHERE bp.id IN (:poolIds)
        ");
        $q->setParameter('poolIds', $poolIds);
        $q->execute();

        $em->flush();
    }
}

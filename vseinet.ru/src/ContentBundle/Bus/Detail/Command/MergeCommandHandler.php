<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailType;

class MergeCommandHandler extends MessageHandler
{
    public function handle(MergeCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($command->id);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }
        if ($detail->getPid()) {
            throw new BadRequestHttpException('Объединение дочерних характеристик не предусмотрено');
        }

        $pool = $em->getRepository(Detail::class)->findBy(['id' => $command->poolIds]);
        if (empty($pool)) {
            throw new BadRequestHttpException('Выберите характеристики для объединения');
        }
        foreach ($pool as $source) {
            if ($source->getPid()) {
                throw new BadRequestHttpException('Объединение дочерних характеристик не предусмотрено');
            }   
            if ($source->getTypeCode() != $detail->getTypeCode()) {
                throw new BadRequestHttpException('Объединять можно только характеристики одного типа');
            }
        }

        foreach ($pool as $source) {
            if ('memo' == $source->getTypeCode()) {
                $this->moveMemos($source, $detail);
            }
            else {
                $type = $em->getRepository(DetailType::class)->find($source->getTypeCode());
                if (!$type instanceof DetailType) {
                    throw new NotFoundHttpException(sprintf('Тип характеристики %s не найден', $source->getTypeCode()));
                }
                if ($type->getIsComposite()) {
                    $sourceDepends = $em->getRepository(Detail::class)->findBy(['pid' => $source->getId()], 'sortOrder');
                    $recipientDepends = $em->getRepository(Detail::class)->findBy(['pid' => $recipient->getId()], 'sortOrder');
                    $this->mergeDepends($sourceDepands, $recipientDepends);
                }
                else {
                    $this->moveValues($source, $recipient);
                }
            }
            $em->remove($source);    
        }
        $em->flush();
    }

    protected function moveMemos(Detail $source, Detail $detail)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:DetailMemoToProduct dm2p
            SET dm2p.detailId = :detailId 
            WHERE dm2p.detailId = :sourceId
        ");
        $q->setParameter('sourceId', $source->getId());
        $q->setParameter('detailId', $detail->getId());
        $q->execute();
    }

    protected function moveValues(Detail $source, Detail $detail)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:DetailToProduct d2p
            SET d2p.detailId = :detailId 
            WHERE d2p.detailId = :sourceId
        ");
        $q->setParameter('sourceId', $source->getId());
        $q->setParameter('detailId', $detail->getId());
        $q->execute();

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:DetailValue dv
            SET dv.detailId = :detailId 
            WHERE dv.detailId = :sourceId
        ");
        $q->setParameter('sourceId', $source->getId());
        $q->setParameter('detailId', $detail->getId());
        $q->execute();
    }

    protected function mergeDepends($sources, $depends)
    {
        foreach ($sources as $index => $source) {
            $this->moveValues($source, $depends[$index]);
        }
    }
}
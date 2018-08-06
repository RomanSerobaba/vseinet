<?php

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailValue;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($command->detailId);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->DetailId));
        }

        $value = $em->getRepository(DetailValue::class)->findOneBy([
            'detailId' => $detail->getId(),
            'value' => $command->value,
        ]);
        if (!$value instanceof DetailValue) {
            $value = new DetailValue();
            $value->setDetailId($detail->getId());
            $value->setValue($command->value);
            $value->setCreatedBy($this->get('user.identity')->getUser()->getId());
            $value->setCreatedAt(new \DateTime());
            $value->setIsVerified(false);
            $em->persist($value);
            $em->flush();
        }

        $this->get('uuid.manager')->saveId($command->uuid, $value->getId());
    }
}
<?php

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;
use ContentBundle\Entity\BaseProductNaming;
use AppBundle\Enum\DetailType;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($command->detailId);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->detailId));
        }

        if (DetailType::CODE_MEMO == $detail->getTypeCode()) {
            throw new BadRequestHttpException('Характеристика с типом `многострочный текст` не может использоваться в наименовании');
        }

        $naming = $em->getRepository(BaseProductNaming::class)->findOneBy(['detailId' => $detail->getId()]);
        if ($naming instanceof BaseProductNaming) {
            throw new BadRequestHttpException(sprintf('Характеристика %s уже используется в наименовании', $detail->getName()));  
        }

        $group = $em->getRepository(DetailGroup::class)->find($detail->getGroupId());

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT MAX(bpn.sortOrder)
            FROM ContentBundle:BaseProductNaming bpn 
            WHERE bpn.categoryId = :categoryId 
        ");
        $q->setParameter('categoryId', $group->getCategoryId());
        try {
            $sortOrder = $q->getSingleScalarResult() + 1;
        } 
        catch (NoResultException $e) {
            $sortOrder = 1;
        }

        $naming = new BaseProductNaming();
        $naming->setCategoryId($group->getCategoryId());
        $naming->setDetailId($detail->getId());
        $naming->setDelimiterAfter('_');
        $naming->setSortOrder($sortOrder);

        $em->persist($naming);
        $em->flush();
    }
}
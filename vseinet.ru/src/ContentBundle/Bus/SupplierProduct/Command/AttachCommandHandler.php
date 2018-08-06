<?php 

namespace ContentBundle\Bus\SupplierProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use SupplyBundle\Entity\SupplierProduct;
use ContentBundle\Entity\BaseProduct;
use SupplyBundle\Entity\SupplierProductTransferLog;

class AttachCommandHandler extends MessageHandler
{
    public function handle(AttachCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplierProducts = $em->getRepository(SupplierProduct::class)->findBy(['id' => $command->ids]);
        if (empty($supplierProducts)) {
            throw new BadRequestHttpException('Выберите хотя бы один товар поставщика');
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %s не найден', $command->baseProductId));
        }

        $user = $this->get('user.identity')->getUser();

        foreach ($supplierProducts as $supplierProduct) {
            $supplierProduct->setBaseProductId($baseProduct->getId());
            $supplierProduct->setIsHidden(false);
            $em->merge($supplierProduct);

            $log = new SupplierProductTransferLog();
            $log->setSupplierProductId($supplierProduct->getId());
            $log->setBaseProductId($baseProduct->getId());
            $log->setTransferedBy($user->getId());
            $log->setTransferedAt(new \DateTime());
            $em->persist($log);
        }

        $em->flush();
    }
}

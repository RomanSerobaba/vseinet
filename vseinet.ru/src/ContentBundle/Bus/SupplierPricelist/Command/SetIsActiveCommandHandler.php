<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierPricelist;

class SetIsActiveCommandHandler extends MessageHandler
{
    public function handle(SetIsActiveCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricelist = $em->getRepository(SupplierPricelist::class)->find($command->id);
        if (!$pricelist instanceof SupplierPricelist) {
            throw new NotFoundHttpException(sprintf('Прайслист %d не найден', $command->id));
        }

        $pricelist->setIsActive($command->isActive);

        $em->persist($pricelist);
        $em->flush();
    }
}

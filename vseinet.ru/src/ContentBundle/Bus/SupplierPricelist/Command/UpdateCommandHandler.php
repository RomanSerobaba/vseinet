<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierPricelist;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricelist = $em->getRepository(SupplierPricelist::class)->find($command->id);
        if (!$pricelist instanceof SupplierPricelist) {
            throw new NotFoundHttpException(sprintf('Прайс-лист %d не найден', $command->id));
        }

        $pricelist->setName($command->name);
        $pricelist->setIsMulti($command->isMulti);

        $em->persist($pricelist);
        $em->flush();
    }
}

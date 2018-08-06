<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierPricelist;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->find($command->supplierId);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException();
        }

        $pricelist = new SupplierPricelist();
        $pricelist->setSupplierId($supplier->getId());
        $pricelist->setName($command->name);
        $pricelist->setIsMulti($command->isMulti);
        $pricelist->setIsActive(true);
        $pricelist->setUploadedQuantity(0);

        $em->persist($pricelist);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $pricelist->getId());
    }
}

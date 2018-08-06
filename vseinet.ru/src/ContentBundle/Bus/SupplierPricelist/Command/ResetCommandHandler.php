<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierPricelist;

class ResetCommandHandler extends MessageHandler
{
    public function handle(ResetCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricelist = $em->getRepository(SupplierPricelist::class)->find($command->id);
        if (!$pricelist instanceof SupplierPricelist) {
            throw new NotFoundHttpException(sprintf('Прайслист поставщик %d не найден', $command->id));
        }

        $pricelist->setUploadStartedAt(null);

        $em->persist($pricelist);
        $em->flush();
    }
}

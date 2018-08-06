<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierPricelist;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricelist = $em->getRepository(SupplierPricelist::class)->find($command->id);
        if (!$pricelist instanceof SupplierPricelist) {
            throw new NotFoundHttpException(sprintf('Прайс-лист %d не найден', $command->id));
        }

        $em->remove($pricelist);
        $em->flush();
    }
}

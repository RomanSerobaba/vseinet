<?php 

namespace ContentBundle\Bus\Brand\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Brand;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %d не найден', $command->id));
        }

        $q = $em->createQuery("
            UPDATE ContentBundle:BaseProduct bp
            SET bp.brandId = NULL 
            WHERE bp.brandId = :brandId 
        ");
        $q->setParameter('brandId', $brand->getId());
        $q->execute();

        $em->remove($brand);
        $em->flush();
    }
}
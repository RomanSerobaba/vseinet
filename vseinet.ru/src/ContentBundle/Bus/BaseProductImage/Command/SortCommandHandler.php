<?php 

namespace ContentBundle\Bus\BaseProductImage\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductImage;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository(BaseProductImage::class)->find($command->id);
        if (!$image instanceof BaseProductImage) {
            throw new NotFoundHttpException(sprintf('Изображение %s не найдено', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        } 
        else {
            $under = $em->getRepository(BaseProductImage::class)->find($command->underId);
            if (!$under instanceof BaseProductImage) {
                throw new NotFoundHttpException(sprintf('Изображение %s не найдено', $command->underId));
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:BaseProductImage bpi
            SET bpi.sortOrder = bpi.sortOrder + 1
            WHERE bpi.baseProductId = :baseProductId AND bpi.sortOrder > :sortOrder
        ");
        $q->setParameter('baseProductId', $image->getBaseProductId());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();

        $image->setSortOrder($sortOrder + 1);

        $em->persist($image);
        $em->flush();
        $em->clear();
        
        $first = $em->getRepository(BaseProductImage::class)->findOneBy([
            'baseProductId' => $image->getBaseProductId()
        ], ['sortOrder' => 'ASC']);
        $first->setSortOrder(1);
        
        $em->persist($first);
        $em->flush();

    }
}

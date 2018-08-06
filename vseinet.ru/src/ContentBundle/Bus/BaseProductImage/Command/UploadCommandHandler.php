<?php 

namespace ContentBundle\Bus\BaseProductImage\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductImage;

class UploadCommandHandler extends MessageHandler
{
    const BASENAME_LENGTH = 8;

    public function handle(UploadCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %s не найден', $command->baseProductId));
        }

        $uniquename = $this->generateUniquename();
        $subdirname = floor($baseProduct->getId() / 1000) .'/'.$baseProduct->getId();

        $filename = $uniquename.'.'.$command->image->guessClientExtension();
        $command->image->move($this->getParameter('product.images.path').'/'.$subdirname, $filename);

        $image = new BaseProductImage();
        $image->setBaseProductId($baseProduct->getId());
        $image->setBasename($subdirname.'/'.$uniquename);
        $image->setWidth(0);
        $image->setHeight(0);
        $image->setSortOrder($this->getMaxSortOrder($baseProduct->getId()) + 1);

        $em->persist($image);
        $em->flush();

        $this->get('old_sound_rabbit_mq.resize.image_producer')->publish(json_encode([
            'id' => $image->getId(),
            'sizes' => $em->getRepository(BaseProductImage::class)->getSizes(),
        ]));

        $this->get('uuid.manager')->saveId($command->uuid, $image->getId());
    }

    protected function generateUniquename()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($pool) - 1;
        $basename = '';
        for ($index = 0; $index < self::BASENAME_LENGTH; $index++) {
            $basename .= $pool[random_int(0, $max)];
        }

        return $basename;
    }

    protected function getMaxSortOrder(int $baseProductId)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT MAX(bpi.sortOrder)
            FROM ContentBundle:BaseProductImage bpi 
            WHERE bpi.baseProductId = :baseProductId
        ");
        $q->setParameter('baseProductId', $baseProductId);

        try {
            return $q->getSingleScalarResult();
        } 
        catch (NoResultException $e) {
            return 0;
        }
    }
}

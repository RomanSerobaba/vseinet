<?php 

namespace ContentBundle\Bus\BrandLogo\Command; 

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Brand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadCommandHandler extends MessageHandler
{
    public function handle(UploadCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %s не найден', $command->id));
        }

        if (!$command->logo instanceof UploadedFile) {
            throw new BadRequestHttpException('Файл изображения не загружен');
        }

        $filename = $brand->getId().'.'.$command->logo->guessClientExtension();
        $command->logo->move($this->getParameter('brand.logo.path'), $filename);

        $brand->setLogo($filename);

        $em->persist($brand);
        $em->flush();
    }
}
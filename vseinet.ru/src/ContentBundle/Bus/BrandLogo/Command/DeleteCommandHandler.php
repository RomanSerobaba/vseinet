<?php 

namespace ContentBundle\Bus\BrandLogo\Command; 

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Brand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {   
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %s не найден', $command->id));
        } 

        if (!$brand->getLogo()) {
            throw new BadRequestHttpException(sprintf('Логотип для бренда %s не загружен', $command->id)); 
        }

        $files = new Finder();
        $files->name($brand->getLogo())->in($this->getParameter('brand.logo.path'));
        if (0 < $files->count()) {
            $filesystem = new Filesystem();
            $filesystem->remove($files);
        }

        $brand->setLogo(null);

        $em->persist($brand);
        $em->flush();
    }
}
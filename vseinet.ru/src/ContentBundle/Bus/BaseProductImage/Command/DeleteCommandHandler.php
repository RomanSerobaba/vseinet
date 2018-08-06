<?php 

namespace ContentBundle\Bus\BaseProductImage\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductImage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository(BaseProductImage::class)->find($command->id);
        if (!$image instanceof BaseProductImage) {
            throw new NotFoundHttpException(sprintf('Изображение товара %s не найдено', $command->id));
        }

        list($dir1, $dir2, $name) = explode('/', $image->getBasename());
        $files = new Finder();
        $files->name($name.'*')->in($this->getParameter('product.images.path').'/'.$dir1.'/'.$dir2);
        if (0 < $files->count()) {
            $filesystem = new Filesystem();
            $filesystem->remove($files);
        }

        $em->remove($image);    
        $em->flush();
        $em->clear();
        
        $first = $em->getRepository(BaseProductImage::class)->findOneBy([
            'baseProductId' => $image->getBaseProductId()
        ], ['sortOrder' => 'ASC']);
        if ($first instanceof BaseProductImage) {
            $first->setSortOrder(1);

            $em->persist($first);
            $em->flush();
        }
    }
}
